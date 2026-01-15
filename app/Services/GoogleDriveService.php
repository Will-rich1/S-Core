<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class GoogleDriveService
{
    protected $disk;
    protected $useGoogleDrive;

    public function __construct()
    {
        // Cek apakah Google Drive sudah dikonfigurasi
        $this->useGoogleDrive = !empty(env('GOOGLE_DRIVE_CLIENT_ID')) 
                                && !empty(env('GOOGLE_DRIVE_CLIENT_SECRET'))
                                && !empty(env('GOOGLE_DRIVE_FOLDER_ID'));
        
        $this->disk = $this->useGoogleDrive ? 'google' : 'public';
    }

    /**
     * Upload file ke Google Drive atau local storage
     * 
     * @param UploadedFile $file
     * @param string $folder
     * @param string|null $studentId - NIM mahasiswa untuk penamaan file
     * @return array ['path' => 'file_path', 'url' => 'public_url']
     */
    public function uploadFile(UploadedFile $file, string $folder = 'certificates', ?string $studentId = null): array
    {
        // Generate filename: YYYYMMDD_NIM_originalname.pdf
        // Contoh: 20260108_22100006_certificate.pdf
        if ($studentId) {
            $date = date('Ymd'); // Format: YYYYMMDD
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filename = "{$date}_{$studentId}_{$originalName}.{$extension}";
        } else {
            // Fallback ke format lama jika studentId tidak ada
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        }
        
        $filePath = $folder . '/' . $filename;

        if ($this->useGoogleDrive) {
            try {
                // Upload ke Google Drive dengan stream untuk file besar
                $stream = fopen($file->getRealPath(), 'r');
                Storage::disk('google')->put($filePath, $stream);
                
                if (is_resource($stream)) {
                    fclose($stream);
                }
                
                // Get file ID dari Google Drive metadata
                $adapter = Storage::disk('google')->getAdapter();
                $metadata = $adapter->getMetadata($filePath);
                
                // Get file ID dari extraMetadata
                $fileId = $metadata->extraMetadata()['id'] ?? null;
                
                \Log::info('Upload result', ['fileId' => $fileId, 'path' => $filePath]);
                
                if ($fileId) {
                    // Set file permissions jadi public
                    $this->makeFilePublic($fileId);
                    
                    $url = "https://drive.google.com/file/d/{$fileId}/preview";
                    
                    \Log::info('Returning file ID', ['fileId' => $fileId]);
                    
                    return [
                        'path' => $fileId,
                        'url' => $url,
                        'storage' => 'google',
                    ];
                }
                
                \Log::warning('File ID is null, using fallback');
            } catch (\Exception $e) {
                \Log::error('Error uploading to Google Drive: ' . $e->getMessage());
                
                // Fallback ke local storage jika Google Drive gagal
                \Log::info('Falling back to local storage');
                $path = $file->storeAs($folder, $filename, 'public');
                
                return [
                    'path' => $path,
                    'url' => Storage::disk('public')->url($path),
                    'storage' => 'local',
                    'fallback' => true,
                ];
            }
            
            // Fallback jika gagal ambil file ID
            return [
                'path' => $filePath,
                'url' => $this->getPublicUrl($filePath),
                'storage' => 'google',
            ];
        } else {
            // Upload ke local storage (fallback)
            $path = $file->storeAs($folder, $filename, 'public');
            
            return [
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
                'storage' => 'local',
            ];
        }
    }

    /**
     * Hapus file dari Google Drive atau local storage
     * 
     * @param string $path
     * @param string $storage
     * @return bool
     */
    public function deleteFile(string $path, string $storage = null): bool
    {
        try {
            // Deteksi storage dari path atau parameter
            if ($storage === 'google' || ($this->useGoogleDrive && !str_starts_with($path, 'certificates/'))) {
                return Storage::disk('google')->delete($path);
            } else {
                return Storage::disk('public')->delete($path);
            }
        } catch (\Exception $e) {
            \Log::error('Error deleting file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get public URL untuk file (Google Drive atau Local)
     * 
     * @param string $path - File path atau file ID
     * @param string|null $storageType - 'google' atau 'local' (if null, akan di-deteksi)
     * @return string
     */
    public function getPublicUrl(string $path, ?string $storageType = null): string
    {
        // Auto-detect storage type jika tidak diberikan
        if ($storageType === null) {
            $storageType = (!str_contains($path, '/') && strlen($path) > 20) ? 'google' : 'local';
        }

        if ($storageType === 'google' || $this->useGoogleDrive) {
            try {
                // Jika path sudah berupa file ID (tidak ada slash dan panjangnya tepat)
                if (!str_contains($path, '/') && strlen($path) > 20) {
                    return "https://drive.google.com/file/d/{$path}/preview";
                }
                
                // Jika masih berupa path, ambil file ID dari metadata
                $adapter = Storage::disk('google')->getAdapter();
                $metadata = $adapter->getMetadata($path);
                
                // Get file ID dari extraMetadata
                $fileId = $metadata->extraMetadata()['id'] ?? null;
                
                if ($fileId) {
                    return "https://drive.google.com/file/d/{$fileId}/preview";
                }
            } catch (\Exception $e) {
                \Log::error('Error getting Google Drive URL: ' . $e->getMessage());
            }
        }
        
        // Untuk local storage atau fallback
        // Pastikan path adalah relative path dari storage/app/public
        if (!str_starts_with($path, 'certificates/')) {
            // Jika tidak ada prefix folder, assume sudah correct
            return Storage::disk('public')->url($path);
        }
        
        return Storage::disk('public')->url($path);
    }

    /**
     * Check apakah menggunakan Google Drive
     * 
     * @return bool
     */
    public function isUsingGoogleDrive(): bool
    {
        return $this->useGoogleDrive;
    }

    /**
     * Get file URL untuk display (view/download)
     * 
     * @param string $path
     * @param string $storage
     * @return string
     */
    public function getFileUrl(string $path, string $storage = null): string
    {
        if ($storage === 'google' || $this->useGoogleDrive) {
            return $this->getPublicUrl($path);
        }
        
        return Storage::disk('public')->url($path);
    }

    /**
     * Set file Google Drive jadi public (anyone with link can view)
     * 
     * @param string $fileId
     * @return void
     */
    protected function makeFilePublic(string $fileId): void
    {
        try {
            $adapter = Storage::disk('google')->getAdapter();
            $service = $adapter->getService();
            
            $permission = new \Google\Service\Drive\Permission([
                'type' => 'anyone',
                'role' => 'reader',
            ]);
            
            $service->permissions->create($fileId, $permission);
            \Log::info("File {$fileId} set to public");
        } catch (\Exception $e) {
            \Log::error("Failed to make file public: " . $e->getMessage());
        }
    }
}
