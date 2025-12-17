<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Penting untuk cek password
use Illuminate\Validation\Rules\Password; // Penting untuk validasi
use App\Models\Submission;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
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
        $activities = $rawActivities->map(function($item) {
            return [
                'id'              => $item->id,
                'mainCategory'    => $item->category->name ?? '-', 
                'subcategory'     => $item->subcategory->name ?? '-',
                'judul'           => $item->title,       
                'keterangan'      => $item->description, 
                'point'           => $item->points_awarded ?? '-',
                'waktu'           => $item->created_at->format('d M Y H:i'),
                'status'          => $item->status,
                'rejectionReason' => $item->rejection_reason,
                'file_url'        => asset('storage/' . $item->certificate_path),
                'category_id'     => $item->student_category_id,
                'certificate_path' => $item->certificate_path, 
                'certificate_original_name' => $item->certificate_original_name,
            ];
        });

        // 3. Ambil Data Kategori untuk Dropdown Form
        $rawCategories = Category::with('subcategories')->where('is_active', true)->orderBy('display_order')->get();
        
        $categoryGroups = $rawCategories->map(function($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'subcategories' => $cat->subcategories->map(function($sub) {
                    return [
                        'id' => $sub->id,
                        'name' => $sub->name,
                        'points' => $sub->points,
                        'description' => $sub->description,
                        'approvedCount' => Submission::where('student_id', Auth::id())
                                                    ->where('student_subcategory_id', $sub->id)
                                                    ->where('status', 'Approved')
                                                    ->count()
                    ];
                })
            ];
        });

        return view('dashboard', compact('user', 'activities', 'stats', 'categoryGroups'));
    }

    /**
     * DASHBOARD ADMIN
     */
    public function adminDashboard()
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
        $submissions = Submission::with(['student', 'category', 'subcategory'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                // LOGIC NIM KE TAHUN: Ambil 2 digit pertama NIM (contoh: 22 -> 2022)
                $nimStr = (string) ($item->student->student_id ?? '00');
                $year = '20' . substr($nimStr, 0, 2);

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
                    'file_url'      => asset('storage/' . $item->certificate_path),
                    'certificate_path' => $item->certificate_path,
                ];
            });

        // 3. AMBIL DATA MAHASISWA & TRACKING POINT (Tab Student Management)
        $students = User::where('role', 'student')
            ->with(['submissions' => function($q) {
                // Kita ambil submissions lengkap untuk modal detail history
                $q->with('category', 'subcategory')->orderBy('created_at', 'desc'); 
            }])
            ->get()
            ->map(function ($student) {
                // Ambil hanya yang Approved untuk perhitungan poin total
                $approvedSubmissions = $student->submissions->where('status', 'Approved');
                
                // LOGIC NIM KE TAHUN
                $nimStr = (string) $student->student_id;
                $year = '20' . substr($nimStr, 0, 2);

                // Hitung Breakdown Poin per Kategori (untuk Tooltip Hover)
                $categoryBreakdown = [];
                foreach ($approvedSubmissions as $sub) {
                    $catName = $sub->category->name ?? 'Other';
                    if (!isset($categoryBreakdown[$catName])) {
                        $categoryBreakdown[$catName] = 0;
                    }
                    $categoryBreakdown[$catName] += $sub->points_awarded;
                }

                return [
                    'id'               => $student->student_id, 
                    'name'             => $student->name,
                    'major'            => $student->major ?? '-', 
                    'year'             => $year, 
                    'approvedPoints'   => $approvedSubmissions->sum('points_awarded'),
                    'approvedCount'    => $approvedSubmissions->count(),
                    'pending'          => $student->submissions->where('status', 'Waiting')->count(),
                    'totalSubmissions' => $student->submissions->count(),
                    'categoryBreakdown'=> $categoryBreakdown, 

                    // DATA UNTUK MODAL VIEW DETAILS (HISTORY)
                    'submissions_list' => $student->submissions->map(function($sub) {
                        return [
                            'id'          => $sub->id, // ID Unik untuk Key AlpineJS
                            'title'       => $sub->title,
                            'category'    => $sub->category->name ?? '-',
                            'subcategory' => $sub->subcategory->name ?? '-',
                            'date'        => Carbon::parse($sub->activity_date)->format('d M Y'),
                            'status'      => $sub->status,
                            'points'      => $sub->points_awarded ?? 0
                        ];
                    })->values()
                ];
            });

        // 4. Statistik Khusus Tab Student (Target Lulus: 20 Poin)
        $studentStats = [
            'passed'  => $students->where('approvedPoints', '>=', 20)->count(),
            'failed'  => $students->where('approvedPoints', '<', 20)->count(),
            'average' => round($students->avg('approvedPoints') ?? 0, 1)
        ];

        // Data Kategori untuk Dropdown di Admin
        $categories = Category::with('subcategories')->where('is_active', true)->get();

        return view('admin_review', compact('submissions', 'stats', 'categories', 'students', 'studentStats'));
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
                'message' => 'Password lama salah (Current password does not match).'
            ], 422); // 422 Unprocessable Entity (Format error standar Laravel)
        }

        // 3. Update Password Baru
        User::where('id', Auth::id())->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json(['message' => 'Password successfully updated!']);
    }
}