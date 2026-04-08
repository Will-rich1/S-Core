<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Submission;

class FileProxyController extends Controller
{
    /**
     * Proxy untuk stream Google Drive file langsung ke browser
     * Streaming = tidak download full file, langsung forward chunks
     */
    public function serveFile($submissionId)
    {
        $submission = Submission::findOrFail($submissionId);

        if (empty($submission->certificate_path)) {
            abort(404, 'File not found');
        }

        $user = Auth::user();
        $isOwner = (int) ($submission->student_id ?? 0) === (int) ($user->id ?? 0);
        if (($user->role ?? null) !== 'admin' && !$isOwner) {
            abort(403, 'Unauthorized');
        }
        
        // Jika file di Google Drive
        if ($submission->storage_type === 'google') {
            try {
                $adapter = Storage::disk('google')->getAdapter();
                $service = $adapter->getService();
                $fileId = $submission->certificate_path;
                
                // Get file metadata untuk ukuran
                $file = $service->files->get($fileId, ['fields' => 'size,mimeType,name']);
                
                // Stream file langsung tanpa download ke memory
                $response = $service->files->get($fileId, ['alt' => 'media']);
                $mimeType = $file->mimeType ?: 'application/pdf';
                $fileName = $submission->certificate_original_name ?: ($file->name ?: 'document.pdf');
                
                return response()->stream(
                    function () use ($response) {
                        $stream = $response->getBody();
                        while (!$stream->eof()) {
                            echo $stream->read(8192); // Read 8KB chunks
                            flush();
                        }
                    },
                    200,
                    [
                        'Content-Type' => $mimeType,
                        'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                        'Content-Length' => (string) ((int) ($file->size ?? 0)),
                        'Cache-Control' => 'public, max-age=3600',
                        'X-Frame-Options' => 'SAMEORIGIN',
                        'Accept-Ranges' => 'bytes',
                    ]
                );
                    
            } catch (\Exception $e) {
                \Log::error('Error streaming Google Drive file: ' . $e->getMessage());
                abort(404, 'File not found');
            }
        }
        
        // Jika file local
        if (Storage::disk('public')->exists($submission->certificate_path)) {
            return response()->file(Storage::disk('public')->path($submission->certificate_path));
        }
        
        abort(404, 'File not found');
    }
}
