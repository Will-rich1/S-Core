<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Penting untuk cek password
use App\Models\Submission;
use App\Models\Category;
use App\Models\User;
use App\Services\GoogleDriveService;
use Carbon\Carbon;
use App\Helpers\SCoreHelper;

class DashboardController extends Controller
{
    private function resolveStudentYear($year, $studentId): ?string
    {
        if (!empty($year)) {
            return (string) $year;
        }

        $nimStr = (string) ($studentId ?? '');
        $nimPrefix = substr($nimStr, 0, 2);

        if (strlen($nimPrefix) === 2 && ctype_digit($nimPrefix)) {
            return '20' . $nimPrefix;
        }

        return null;
    }

    private function calculateSemesterFromYear($year, int $semesterOffset = 0): ?int
    {
        if (empty($year) || !is_numeric($year)) {
            return null;
        }

        $currentYear = (int) now()->year;
        $currentMonth = (int) now()->month;
        $batchYear = (int) $year;

        // Perhitungan berdasarkan kalender akademik Indonesia:
        // Agustus-Desember = semester ganjil, Januari-Juli = semester genap.
        // Contoh Maret 2026: 2022->8, 2023->6, 2024->4, 2025->2.
        if ($currentMonth >= 8) {
            $semester = (($currentYear - $batchYear) * 2) + 1;
        } else {
            $semester = (($currentYear - $batchYear) * 2);
        }

        return max(1, $semester + max(0, $semesterOffset));
    }

    /**
     * DASHBOARD MAHASISWA
     */
    public function studentDashboard()
    {
        // CEK ROLE: Jika bukan student, lempar ke admin
        if (Auth::user()->role !== 'student') {
            return redirect()->route('admin.dashboard');
        }

        $user = Auth::user();
        $resolvedYear = $this->resolveStudentYear($user->year, $user->student_id);
        $user->year = $resolvedYear;
        $user->semester = $this->calculateSemesterFromYear($resolvedYear, (int) ($user->semester_offset ?? 0));

        // 1. Ambil Data Statistik
        $stats = [
            'approvedPoints' => Submission::where('student_id', $user->id)->where('status', 'Approved')->sum('points_awarded'),
            'waiting'        => Submission::where('student_id', $user->id)->where('status', 'Waiting')->count(),
            'approved'       => Submission::where('student_id', $user->id)->where('status', 'Approved')->count(),
            'rejected'       => Submission::where('student_id', $user->id)->where('status', 'Rejected')->count(),
        ];

        // 2. Ambil List Aktivitas
        $rawActivities = Submission::with(['category', 'subcategory'])
            ->where('student_id', $user->id)
            ->orderBy('created_at', 'desc') // Urutkan dari yang terbaru
            ->get();

        // Mapping Data untuk View Mahasiswa
        $googleDriveService = app(GoogleDriveService::class);
        $activities = $rawActivities->map(function($item) use ($googleDriveService) {
            // Generate viewing URL - prioritas: certificate_url > generated from path
            if ($item->certificate_url) {
                $fileUrl = $item->certificate_url;
            } elseif ($item->certificate_path && !empty($item->certificate_path)) {
                // Generate URL dari certificate_path dan storage_type
                $storageType = $item->storage_type ?? 'local';
                $fileUrl = $googleDriveService->getPublicUrl($item->certificate_path, $storageType);
            } else {
                $fileUrl = null;
            }
            
            return [
                'id'              => $item->id,
                'mainCategory'    => $item->category->name ?? '-', 
                'subcategory'     => $item->subcategory->name ?? '-',
                'judul'           => $item->title,       
                'keterangan'      => $item->description, 
                'point'           => $item->points_awarded ?? '-',
                'waktu'           => $item->created_at->format('d M Y H:i'),
                'activityDate'    => $item->activity_date ? Carbon::parse($item->activity_date)->format('Y-m-d') : '-',
                'status'          => $item->status,
                'rejectionReason' => $item->rejection_reason,
                'pointAdjustmentReason' => $item->points_adjustment_reason,
                'file_url'        => $fileUrl,
                'certificate'     => $item->certificate_original_name ?? 'document.pdf',
                'category_id'     => $item->student_category_id,
                'certificate_path' => $item->certificate_path, 
                'certificate_original_name' => $item->certificate_original_name,
            ];
        });

        // 3. Ambil Data Kategori untuk Dropdown Form
        $rawCategories = Category::with('subcategories')->where('is_active', true)->orderBy('display_order')->get();

        // Precompute approved counts per subcategory for current student.
        $approvedSubmissions = $rawActivities->where('status', 'Approved')->values();
        $approvedCountBySubcategoryId = $approvedSubmissions
            ->groupBy('student_subcategory_id')
            ->map(fn ($items) => $items->count());

        // Legacy CSV migration entries are grouped as one synthetic subcategory: "Migrasi".
        // approvedCount is fixed to 1, while points follows the student's migrated points per main category.
        $migrationPointsByCategoryId = $approvedSubmissions
            ->filter(function ($item) {
                $title = strtolower(trim((string) ($item->title ?? '')));
                $subcategoryName = strtolower(trim((string) ($item->subcategory->name ?? '')));

                return str_starts_with($title, 'migrasi data csv') || str_starts_with($subcategoryName, 'migrasi');
            })
            ->groupBy('student_category_id')
            ->map(fn ($items) => round((float) $items->sum('points_awarded'), 2));
        
        $categoryGroups = $rawCategories->map(function($cat) use ($approvedCountBySubcategoryId, $migrationPointsByCategoryId) {
            $subcategories = $cat->subcategories->map(function($sub) use ($approvedCountBySubcategoryId) {
                return [
                    'id' => $sub->id,
                    'name' => $sub->name,
                    'points' => $sub->points,
                    'description' => $sub->description,
                    'approvedCount' => (int) ($approvedCountBySubcategoryId[$sub->id] ?? 0),
                ];
            })->values();

            $migrationPoints = (float) ($migrationPointsByCategoryId[$cat->id] ?? 0);
            if ($migrationPoints > 0) {
                $migrationIndex = $subcategories->search(function ($sub) {
                    return strtolower(trim((string) ($sub['name'] ?? ''))) === 'migrasi';
                });

                if ($migrationIndex !== false) {
                    $subcategories[$migrationIndex]['points'] = $migrationPoints;
                    $subcategories[$migrationIndex]['approvedCount'] = 1;

                    if (empty($subcategories[$migrationIndex]['description'])) {
                        $subcategories[$migrationIndex]['description'] = 'Subkategori otomatis untuk data migrasi lama';
                    }
                } else {
                    $subcategories->push([
                        'id' => 'migration-' . $cat->id,
                        'name' => 'Migrasi',
                        'points' => $migrationPoints,
                        'description' => 'Subkategori otomatis untuk data migrasi lama',
                        'approvedCount' => 1,
                    ]);
                }
            }

            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'subcategories' => $subcategories,
            ];
        });

        return view('dashboard', compact('user', 'activities', 'stats', 'categoryGroups'));
    }

    /**
     * DASHBOARD ADMIN
     */
    public function adminDashboard(Request $request)
    {
        // CEK ROLE: Jika bukan admin, lempar ke dashboard mahasiswa
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }

        // 1. Statistik Umum (Header Dashboard)
        $stats = [
            'total'    => Submission::count(),
            'waiting'  => Submission::where('status', 'Waiting')->count(),
            'approved' => Submission::where('status', 'Approved')->count(),
            'rejected' => Submission::where('status', 'Rejected')->count(),
        ];

        // 2. Ambil SEMUA Submission untuk direview (Tab Review)
        $googleDriveService = app(GoogleDriveService::class);
        $submissions = Submission::with(['student', 'category', 'subcategory'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) use ($googleDriveService) {
                $year = $this->resolveStudentYear($item->student->year ?? null, $item->student->student_id ?? null);

                // Generate viewing URL
                if ($item->certificate_url) {
                    $fileUrl = $item->certificate_url;
                } elseif ($item->certificate_path && !empty($item->certificate_path)) {
                    $storageType = $item->storage_type ?? 'local';
                    $fileUrl = $googleDriveService->getPublicUrl($item->certificate_path, $storageType);
                } else {
                    $fileUrl = null;
                }

                return [
                    'id'            => $item->id,
                    'studentId'     => $item->student->student_id ?? '-',
                    'studentName'   => $item->student->name ?? 'Unknown',
                    'major'         => $item->student->major ?? '-',
                    'year'          => $year, 
                    
                    'mainCategory'  => $item->category->name ?? '-',
                    'subcategory'   => $item->subcategory->name ?? '-',
                    
                    'judul'         => $item->title,
                    'keterangan'    => $item->description,
                    'point'         => $item->points_awarded,
                    'suggestedPoint'=> $item->subcategory->points ?? 0,
                    
                    'waktu'         => $item->created_at->format('d M Y H:i'),
                    'activityDate'  => $item->activity_date ? Carbon::parse($item->activity_date)->format('Y-m-d') : '-',
                    'submittedDate' => $item->created_at->format('Y-m-d'),
                    
                    'status'        => $item->status,
                    'certificate'   => $item->certificate_original_name,
                    'file_url'      => $fileUrl,
                    'certificate_path' => $item->certificate_path,
                ];
            });

        // 3. AMBIL DATA MAHASISWA & TRACKING POINT (Tab Student Management)
        $allowedPerPage = [25, 50, 100, 250, 500];
        $studentsPerPage = (int) $request->query('students_per_page', 25);
        if (!in_array($studentsPerPage, $allowedPerPage, true)) {
            $studentsPerPage = 25;
        }

        $studentSearch = trim((string) $request->query('student_search', ''));
        $majorFilter = (string) $request->query('major_filter', '');
        $yearFilterMode = (string) $request->query('year_mode', 'all');
        $yearFilter = trim((string) $request->query('year_filter', ''));

        $studentsPage = (int) $request->query('students_page', 1);
        if ($studentsPage < 1) {
            $studentsPage = 1;
        }

        $studentsQuery = User::where('role', 'student');

        if ($studentSearch !== '') {
            $studentsQuery->where(function ($q) use ($studentSearch) {
                $q->where('name', 'like', '%' . $studentSearch . '%')
                    ->orWhere('student_id', 'like', '%' . $studentSearch . '%');
            });
        }

        if ($majorFilter !== '') {
            $studentsQuery->where('major', $majorFilter);
        }

        if ($yearFilterMode === 'specific' && $yearFilter !== '') {
            $studentsQuery->where(function ($q) use ($yearFilter) {
                $q->where('year', $yearFilter)
                    ->orWhere(function ($q2) use ($yearFilter) {
                        $q2->whereNull('year')
                            ->whereRaw("CONCAT('20', SUBSTRING(CAST(student_id AS CHAR), 1, 2)) = ?", [$yearFilter]);
                    });
            });
        }

        $studentsPaginator = $studentsQuery
            ->with(['submissions' => function($q) {
                // Kita ambil submissions lengkap untuk modal detail history
                $q->with('category', 'subcategory')->orderBy('created_at', 'desc'); 
            }])
            ->orderBy('student_id')
            ->paginate($studentsPerPage, ['*'], 'students_page', $studentsPage);

        $students = $studentsPaginator->getCollection()
            ->map(function ($student) use ($googleDriveService) {
                // Ambil hanya yang Approved untuk perhitungan poin total
                $approvedSubmissions = $student->submissions->where('status', 'Approved');
                
                $year = $this->resolveStudentYear($student->year, $student->student_id);
                $semester = $this->calculateSemesterFromYear($year, (int) ($student->semester_offset ?? 0));

                // Hitung Breakdown Poin per Kategori (untuk Tooltip Hover)
                $categoryBreakdown = [];
                foreach ($approvedSubmissions as $sub) {
                    $catName = $sub->category->name ?? 'Other';
                    $catId = $sub->category->id ?? null;
                    if (!isset($categoryBreakdown[$catId])) {
                        $categoryBreakdown[$catId] = [
                            'categoryName' => $catName,
                            'count' => 0,
                            'points' => 0
                        ];
                    }
                    $categoryBreakdown[$catId]['count']++;
                    $categoryBreakdown[$catId]['points'] += $sub->points_awarded;
                }

                return [
                    'id'               => $student->student_id, 
                    'name'             => $student->name,
                    'major'            => $student->major ?? '-', 
                    'semester'         => $semester,
                    'year'             => $year, 
                    'approvedPoints'   => $approvedSubmissions->sum('points_awarded'),
                    'approvedCount'    => $approvedSubmissions->count(),
                    'pending'          => $student->submissions->where('status', 'Waiting')->count(),
                    'totalSubmissions' => $student->submissions->count(),
                    'categoryBreakdown'=> $categoryBreakdown, 

                    // DATA UNTUK MODAL VIEW DETAILS (HISTORY)
                    'submissions_list' => $student->submissions->map(function($sub) use ($googleDriveService) {
                        if ($sub->certificate_url) {
                            $fileUrl = $sub->certificate_url;
                        } elseif ($sub->certificate_path && !empty($sub->certificate_path)) {
                            $storageType = $sub->storage_type ?? 'local';
                            $fileUrl = $googleDriveService->getPublicUrl($sub->certificate_path, $storageType);
                        } else {
                            $fileUrl = null;
                        }

                        return [
                            'id'          => $sub->id, // ID Unik untuk Key AlpineJS
                            'title'       => $sub->title,
                            'category'    => $sub->category->name ?? '-',
                            'mainCategory'=> $sub->category->name ?? '-',
                            'subcategory' => $sub->subcategory->name ?? '-',
                            'description' => $sub->description ?? '-',
                            'date'        => $sub->activity_date ? Carbon::parse($sub->activity_date)->format('d M Y') : '-',
                            'activityDate'=> $sub->activity_date ? Carbon::parse($sub->activity_date)->format('Y-m-d') : '-',
                            'submittedDate' => $sub->created_at->format('Y-m-d'),
                            'waktu'       => $sub->created_at->format('d M Y H:i'),
                            'status'      => $sub->status,
                            'points'      => $sub->points_awarded ?? 0,
                            'pointAdjustmentReason' => $sub->points_adjustment_reason,
                            'certificate' => $sub->certificate_original_name ?? 'document.pdf',
                            'certificate_path' => $sub->certificate_path,
                            'file_url'    => $fileUrl
                        ];
                    })->values()
                ];
            })
            ->values();

        // Ambil daftar angkatan global dari SELURUH data mahasiswa (bukan hanya halaman aktif).
        $availableStudentYears = User::where('role', 'student')
            ->get(['year', 'student_id'])
            ->map(function ($student) {
                return $this->resolveStudentYear($student->year, $student->student_id);
            })
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        $studentsPagination = [
            'currentPage' => $studentsPaginator->currentPage(),
            'lastPage' => $studentsPaginator->lastPage(),
            'perPage' => $studentsPaginator->perPage(),
            'total' => $studentsPaginator->total(),
            'from' => $studentsPaginator->firstItem() ?? 0,
            'to' => $studentsPaginator->lastItem() ?? 0,
            'hasMorePages' => $studentsPaginator->hasMorePages(),
        ];

        $studentsFilters = [
            'studentSearch' => $studentSearch,
            'majorFilter' => $majorFilter,
            'yearFilterMode' => in_array($yearFilterMode, ['all', 'specific'], true) ? $yearFilterMode : 'all',
            'yearFilter' => $yearFilter,
        ];

        // 4. Statistik Khusus Tab Student (Target Lulus: Points dan Kategori dari Settings)
        $minPoints = SCoreHelper::getMinPointsRequired();
        $minCategories = SCoreHelper::getMinCategoriesRequired();
        
        $studentStats = [
            'passed'  => $students->filter(function($s) use ($minPoints, $minCategories) {
                $studentMinCategories = SCoreHelper::getMinCategoriesRequiredForYear($s['year'] ?? null, $s['id'] ?? null, $minCategories);
                return ($s['approvedPoints'] >= $minPoints) && (count($s['categoryBreakdown']) >= $studentMinCategories);
            })->count(),
            'failed'  => $students->filter(function($s) use ($minPoints, $minCategories) {
                $studentMinCategories = SCoreHelper::getMinCategoriesRequiredForYear($s['year'] ?? null, $s['id'] ?? null, $minCategories);
                return !(($s['approvedPoints'] >= $minPoints) && (count($s['categoryBreakdown']) >= $studentMinCategories));
            })->count(),
            'average' => round($students->avg('approvedPoints') ?? 0, 1)
        ];

        // Data Kategori untuk Dropdown di Admin
        $categories = Category::with('subcategories')->where('is_active', true)->get();

        // Get S-Core settings
        $scoreSettings = [
            'minPoints' => $minPoints,
            'minCategories' => $minCategories
        ];

        return view('admin_review', compact('submissions', 'stats', 'categories', 'students', 'studentStats', 'scoreSettings', 'studentsPagination', 'availableStudentYears', 'studentsFilters'));
    }

    /**
     * UPDATE PASSWORD USER (MAHASISWA & ADMIN)
     */
    public function updatePassword(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed', // 'confirmed' otomatis cek new_password_confirmation
        ]);

        // 2. Cek apakah password lama benar
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return response()->json([
                'message' => 'Current password does not match.'
            ], 422); // 422 Unprocessable Entity (Format error standar Laravel)
        }

        // 3. Update Password Baru
        User::where('id', Auth::id())->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json(['message' => 'Password successfully updated!']);
    }

    /**
     * BULK SCORE - Tambah S-Core untuk banyak mahasiswa sekaligus
     */
    public function bulkScore(Request $request)
    {
        // Validasi input
        $request->validate([
            'selectedMajor' => 'nullable|string|in:STI,BD,KWU',
            'selectedYear' => 'nullable|string',
            'selectedShift' => 'nullable|string|in:siang,sore',
            'mainCategory' => 'required|exists:categories,id',
            'subcategory' => 'required|exists:subcategories,id',
            'activityTitle' => 'required|string|max:500',
            'description' => 'required|string',
            'activityDate' => 'required|date',
            'certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        try {
            // 1. Build query untuk target students
            $query = User::where('role', 'student');

            if ($request->selectedMajor) {
                $query->where('major', $request->selectedMajor);
            }

            if ($request->selectedYear) {
                $query->where('year', $request->selectedYear);
            }

            if ($request->selectedShift) {
                $query->where('shift', $request->selectedShift);
            }

            $targetStudents = $query->get();

            if ($targetStudents->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No students found matching the selected filters.'
                ], 404);
            }

            // 2. Get subcategory to retrieve points
            $subcategory = \App\Models\Subcategory::findOrFail($request->subcategory);
            $category = \App\Models\Category::findOrFail($request->mainCategory);

            // 3. Handle certificate upload to Google Drive (optional)
            $certificatePath = null;
            $certificateOriginalName = null;
            $certificateUrl = null;
            $storageType = 'google';
            
            if ($request->hasFile('certificate')) {
                $googleDriveService = app(GoogleDriveService::class);
                $uploadResult = $googleDriveService->uploadFile(
                    $request->file('certificate'),
                    'bulk_certificates'
                );
                
                $certificatePath = $uploadResult['path'];
                $certificateUrl = $uploadResult['url'] ?? null;
                $certificateOriginalName = $request->file('certificate')->getClientOriginalName();
                $storageType = $uploadResult['storage'] ?? 'google';
            }

            // 4. Create submissions for all target students
            $createdCount = 0;
            foreach ($targetStudents as $student) {
                Submission::create([
                    'student_id' => $student->id,
                    'student_category_id' => $category->id,
                    'student_subcategory_id' => $subcategory->id,
                    'assigned_category_id' => $category->id,
                    'assigned_subcategory_id' => $subcategory->id,
                    'title' => $request->activityTitle,
                    'description' => $request->description,
                    'activity_date' => $request->activityDate,
                    'semester_cycle' => max(0, (int) ($student->semester_offset ?? 0)),
                    'certificate_path' => $certificatePath,
                    'certificate_url' => $certificateUrl,
                    'certificate_original_name' => $certificateOriginalName,
                    'storage_type' => $storageType,
                    'status' => 'Approved', // Auto-approved by admin
                    'points_awarded' => $subcategory->points,
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                ]);
                $createdCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully created {$createdCount} submissions for {$createdCount} student(s).",
                'count' => $createdCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating bulk submissions: ' . $e->getMessage()
            ], 500);
        }
    }
}
