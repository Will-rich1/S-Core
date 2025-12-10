<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Submission;
use App\Models\Category;
use App\Models\User;

class DashboardController extends Controller
{
    public function studentDashboard()
    {
        // CEK ROLE: Jika bukan student, lempar ke admin
        if (Auth::user()->role !== 'student') {
            return redirect()->route('admin.dashboard');
        }

        $user = Auth::user();

        // Ambil Data Submission
        $activities = Submission::where('student_id', $user->id)
            ->with(['category', 'subcategory'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'mainCategory' => $item->category->name ?? '-',
                    'subcategory' => $item->subcategory->name ?? '-',
                    'judul' => $item->title,
                    'keterangan' => $item->description,
                    'point' => $item->points_awarded,
                    'waktu' => $item->created_at->format('d M Y H:i'),
                    'status' => $item->status,
                    'rejectionReason' => $item->rejection_reason,
                    'file_url' => asset('storage/' . $item->certificate_path),
                ];
            });

        // Hitung Statistik
        $stats = [
            'approvedPoints' => $user->submissions()->where('status', 'Approved')->sum('points_awarded'),
            'waiting' => $user->submissions()->where('status', 'Waiting')->count(),
            'approved' => $user->submissions()->where('status', 'Approved')->count(),
            'rejected' => $user->submissions()->where('status', 'Rejected')->count(),
        ];

        // Ambil Data Kategori untuk Form Dropdown
        $rawCategories = Category::with('subcategories')->where('is_active', true)->orderBy('display_order')->get();
        
        // Format Data Kategori sesuai format AlpineJS di blade kamu
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
                        // Hitung progress mahasiswa di kategori ini
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

    public function adminDashboard()
    {
        // CEK ROLE: Jika bukan admin, lempar ke dashboard mahasiswa
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }

        // Ambil SEMUA Submission untuk direview
        $submissions = Submission::with(['student', 'category', 'subcategory'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'studentId' => $item->student->student_id ?? '-',
                    'studentName' => $item->student->name ?? 'Unknown',
                    'major' => $item->student->major ?? '-',
                    'year' => $item->student->year ?? '-',
                    'mainCategory' => $item->category->name ?? '-',
                    'subcategory' => $item->subcategory->name ?? '-',
                    'judul' => $item->title,
                    'keterangan' => $item->description,
                    'point' => $item->points_awarded,
                    'suggestedPoint' => $item->subcategory->points ?? 0,
                    'waktu' => $item->created_at->format('d M Y H:i'),
                    'activityDate' => \Carbon\Carbon::parse($item->activity_date)->format('Y-m-d'),
                    'submittedDate' => $item->created_at->format('Y-m-d'),
                    'status' => $item->status,
                    'certificate' => $item->certificate_original_name,
                    'file_url' => asset('storage/' . $item->certificate_path),
                    'kategori' => $item->category->name ?? '-',
                ];
            });

        // Statistik Admin
        $stats = [
            'total' => Submission::count(),
            'waiting' => Submission::where('status', 'Waiting')->count(),
            'approved' => Submission::where('status', 'Approved')->count(),
            'rejected' => Submission::where('status', 'Rejected')->count(),
        ];

        $categories = Category::with('subcategories')->get();

        return view('admin_review', compact('submissions', 'stats', 'categories'));
    }
}