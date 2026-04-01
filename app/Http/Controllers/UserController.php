<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Submission;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    // 1. Simpan Student Manual
    public function storeStudent(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','email','unique:users','regex:/^[^@]+@itbss\.ac\.id$/'],
            'password' => 'required|min:6',
            'student_id' => 'required|string|unique:users', // Tambahkan unique
            'major' => 'required|in:STI,BD,KWU',
            'batch_year' => 'required|integer', // Di form namanya batch_year
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
            'student_id' => $validated['student_id'],
            'major' => $validated['major'],
            'year' => $validated['batch_year'], // Petakan ke kolom 'year' di DB
        ]);

        return redirect()->back()->with('success', 'Student account created successfully!');
    }

    // Admin can reset/overwrite a user's password (returns new password when generated)
    public function resetPassword(Request $request, $id)
    {
        // Only allow admin users to perform this action
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        Log::info('resetPassword called', ['admin_id' => Auth::id(), 'target_user' => $id, 'ip' => request()->ip()]);

        $request->validate([
            'password' => 'required|string|min:6'
        ]);

        // Support both internal primary-key id and student_id passed from UI
        $user = User::find($id);
        if (!$user) {
            // try lookup by student_id (students use student_id as external identifier)
            $user = User::where('student_id', $id)->firstOrFail();
        }

        // Use the provided password
        $provided = $request->input('password');
        $newPassword = $provided;
        $generated = false;

        $user->password = Hash::make($newPassword);
        $user->save();

        Log::info('resetPassword saved', ['admin_id' => Auth::id(), 'target_user' => $user->id]);

        // Log activity (best-effort)
        try {
            $now = now();
            if (Schema::hasColumn('activity_logs', 'entity_type')) {
                DB::table('activity_logs')->insert([
                    'user_id' => Auth::id(),
                    'action' => 'admin_reset_password',
                    'entity_type' => 'user',
                    'entity_id' => $user->id,
                    'details' => json_encode(['user_provided' => true]),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            } elseif (Schema::hasColumn('activity_logs', 'target_type')) {
                DB::table('activity_logs')->insert([
                    'user_id' => Auth::id(),
                    'action' => 'admin_reset_password',
                    'target_type' => 'user',
                    'target_id' => $user->id,
                    'message' => json_encode(['user_provided' => true]),
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            } else {
                DB::table('activity_logs')->insert([
                    'user_id' => Auth::id(),
                    'action' => 'admin_reset_password',
                    'details' => json_encode(['user_provided' => true]),
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to insert activity_log for resetPassword: '.$e->getMessage());
        }

        return response()->json([
            'message' => 'Password reset successfully',
            'generated' => false
        ]);
    }

    // 2. Simpan Admin Manual
    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','email','unique:users','regex:/^[^@]+@itbss\.ac\.id$/'],
            'password' => 'required|min:6|confirmed', // Pastikan input confirm password ada
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'student_id' => null,
            'major' => null,
            'year' => null,
        ]);

        return redirect()->back()->with('success', 'Admin account created successfully!');
    }

    // 3. Import Students dari CSV
    public function importStudents(Request $request)
    {
        // Remove execution time limit for large CSV imports (bcrypt hashing is slow)
        set_time_limit(0);

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');

        if ($handle === false) {
            return back()->withErrors(['csv_file' => 'Failed to read CSV file.']);
        }

        $count = 0;
        $scoreCount = 0;
        $errors = [];
        $lineNumber = 0;

        // Mapping CSV column index => [category_name, subcategory_name]
        // For single-subcategory categories, use their only subcategory directly
        // For multi-subcategory categories, use "Migrasi ..." subcategory
        $scoreColumns = [
            6  => ['category' => 'OrKeSS (WAJIB)', 'subcategory' => 'OrKeSS'],
            7  => ['category' => 'Retreat (WAJIB)', 'subcategory' => 'Retreat'],
            8  => ['category' => 'Kegiatan Ilmiah dan Penalaran', 'subcategory' => 'Migrasi Kegiatan Ilmiah dan Penalaran'],
            9  => ['category' => 'Performance, Pengembangan, dan Perlombaan', 'subcategory' => 'Migrasi Performance, Pengembangan, dan Perlombaan'],
            10 => ['category' => 'Kepengurusan Organisasi/Kepanitiaan', 'subcategory' => 'Migrasi Kepengurusan Organisasi/Kepanitiaan'],
            11 => ['category' => 'Kegiatan Sosial Kemasyarakatan', 'subcategory' => 'Migrasi Kegiatan Sosial Kemasyarakatan'],
        ];

        // Pre-load category and subcategory IDs for performance
        $categoryMap = [];
        $subcategoryMap = [];
        foreach ($scoreColumns as $idx => $mapping) {
            $cat = Category::where('name', $mapping['category'])->first();
            if ($cat) {
                $categoryMap[$idx] = $cat->id;
                $sub = Subcategory::where('category_id', $cat->id)
                    ->where('name', $mapping['subcategory'])
                    ->first();
                if (!$sub) {
                    // Auto-create Migrasi subcategory if it doesn't exist
                    $sub = Subcategory::create([
                        'category_id' => $cat->id,
                        'name' => $mapping['subcategory'],
                        'points' => 1,
                        'description' => 'Data migrasi dari CSV import',
                        'is_active' => true,
                    ]);
                }
                $subcategoryMap[$idx] = $sub->id;
            }
        }

        while (($row = fgetcsv($handle, 5000, ',')) !== false) {
            $lineNumber++;
            
            // Skip header row if exists
            if ($lineNumber == 1 && in_array(strtolower(trim($row[0])), ['name', 'nama'])) {
                continue;
            }

            // Minimum 6 columns required (user data), score columns are optional
            if (count($row) < 6) {
                $errors[] = "Line $lineNumber: Incomplete data (minimum 6 columns required)";
                continue;
            }

            $email = trim($row[1]);
            $major = trim($row[4]);
            
            if (User::where('email', $email)->exists()) {
                $errors[] = "Line $lineNumber: Email '$email' already exists";
                continue;
            }

            if (!preg_match('/^[^@]+@itbss\.ac\.id$/', $email)) {
                $errors[] = "Line $lineNumber: Email '$email' must be an @itbss.ac.id address";
                continue;
            }

            if (!in_array($major, ['STI', 'BD', 'KWU'])) {
                $errors[] = "Line $lineNumber: Invalid major '$major'";
                continue;
            }

            try {
                DB::beginTransaction();

                $user = User::create([
                    'name'       => trim($row[0]),
                    'email'      => $email,
                    'password'   => Hash::make(trim($row[2])),
                    'role'       => 'student',
                    'student_id' => trim($row[3]),
                    'major'      => $major,
                    'year'       => (int)trim($row[5]),
                ]);
                $count++;

                // Process score columns (index 6-11) if they exist
                foreach ($scoreColumns as $colIdx => $mapping) {
                    if (!isset($row[$colIdx]) || trim($row[$colIdx]) === '') {
                        continue; // Skip empty scores
                    }

                    // Support both dot and comma as decimal separator (e.g. 0.6 or 0,6)
                    $rawValue = str_replace(',', '.', trim($row[$colIdx]));
                    $points = floatval($rawValue);
                    if ($points <= 0) {
                        continue;
                    }

                    if (!isset($categoryMap[$colIdx]) || !isset($subcategoryMap[$colIdx])) {
                        $errors[] = "Line $lineNumber: Category '{$mapping['category']}' not found in database";
                        continue;
                    }

                    Submission::create([
                        'student_id'            => $user->id,
                        'student_category_id'   => $categoryMap[$colIdx],
                        'student_subcategory_id'=> $subcategoryMap[$colIdx],
                        'assigned_category_id'  => $categoryMap[$colIdx],
                        'assigned_subcategory_id'=> $subcategoryMap[$colIdx],
                        'title'                 => 'Migrasi Data CSV - ' . $mapping['category'],
                        'description'           => 'Data S-Core dimigrasikan dari CSV import oleh admin',
                        'activity_date'         => now()->toDateString(),
                        'semester_cycle'        => max(0, (int) ($user->semester_offset ?? 0)),
                        'status'                => 'Approved',
                        'points_awarded'        => $points,
                        'reviewed_by'           => Auth::id(),
                        'reviewed_at'           => now(),
                    ]);
                    $scoreCount++;
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $errors[] = "Line $lineNumber: " . $e->getMessage();
                continue;
            }
        }

        fclose($handle);

        $message = "Successfully imported $count student(s)";
        if ($scoreCount > 0) {
            $message .= " with $scoreCount score submission(s)";
        }
        $message .= ".";
        if (count($errors) > 0) {
            $message .= " Errors: " . implode(', ', array_slice($errors, 0, 5)) . (count($errors) > 5 ? "..." : "");
        }

        return back()->with('success', $message);
    }

    public function deleteStudents(Request $request)
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'required|string'
        ]);

        $studentIds = $request->input('student_ids');
        $deletedCount = 0;

        try {
            foreach ($studentIds as $studentId) {
                $user = User::where('student_id', $studentId)->where('role', 'student')->first();
                if ($user) {
                    $user->delete();
                    $deletedCount++;
                }
            }

            return response()->json(['message' => 'Successfully deleted students', 'deleted_count' => $deletedCount]);
        } catch (\Exception $e) {
            Log::error('Error deleting students: '.$e->getMessage());
            return response()->json(['message' => 'Failed to delete'], 500);
        }
    }

    public function promoteSemester(Request $request)
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        try {
            $affected = User::where('role', 'student')->increment('semester_offset', 1);

            return response()->json([
                'message' => 'Semester semua mahasiswa berhasil dinaikkan +1.',
                'affected_count' => $affected,
            ]);
        } catch (\Exception $e) {
            Log::error('Error promoting semester: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal menaikkan semester.'], 500);
        }
    }

    public function demoteSemester(Request $request)
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        try {
            $affected = User::where('role', 'student')
                ->where('semester_offset', '>', 0)
                ->decrement('semester_offset', 1);

            return response()->json([
                'message' => 'Semester mahasiswa berhasil diturunkan -1.',
                'affected_count' => $affected,
            ]);
        } catch (\Exception $e) {
            Log::error('Error demoting semester: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal menurunkan semester.'], 500);
        }
    }

    public function updateAcademicStatus(Request $request, $studentId)
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            abort(403);
        }

        if (!Schema::hasColumn('users', 'academic_status')) {
            $message = 'Kolom academic_status belum tersedia. Jalankan migrasi terlebih dahulu (php artisan migrate).';
            if (!($request->expectsJson() || $request->wantsJson())) {
                return redirect()->back()->with('error', $message);
            }

            return response()->json(['message' => $message], 422);
        }

        $validated = $request->validate([
            'academic_status' => 'required|in:active,on_leave,graduated',
        ]);

        try {
            $student = User::where('role', 'student')
                ->where('student_id', $studentId)
                ->firstOrFail();

            $student->academic_status = $validated['academic_status'];
            $student->save();

            if (!($request->expectsJson() || $request->wantsJson())) {
                return redirect()->back()->with('success', 'Status akademik mahasiswa berhasil diperbarui.');
            }

            return response()->json([
                'message' => 'Status akademik mahasiswa berhasil diperbarui.',
                'academic_status' => $student->academic_status,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating academic status: ' . $e->getMessage());

            if (!($request->expectsJson() || $request->wantsJson())) {
                return redirect()->back()->with('error', 'Gagal memperbarui status akademik.');
            }

            return response()->json(['message' => 'Gagal memperbarui status akademik.'], 500);
        }
    }

    public function bulkUpdateAcademicStatus(Request $request)
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if (!Schema::hasColumn('users', 'academic_status')) {
            return response()->json([
                'message' => 'Kolom academic_status belum tersedia. Jalankan migrasi terlebih dahulu (php artisan migrate).'
            ], 422);
        }

        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'required|string',
            'academic_status' => 'required|in:active,on_leave,graduated',
        ]);

        try {
            $affected = User::where('role', 'student')
                ->whereIn('student_id', $validated['student_ids'])
                ->update(['academic_status' => $validated['academic_status']]);

            return response()->json([
                'message' => 'Status akademik mahasiswa terpilih berhasil diperbarui.',
                'affected_count' => $affected,
                'academic_status' => $validated['academic_status'],
            ]);
        } catch (\Exception $e) {
            Log::error('Error bulk updating academic status: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal memperbarui status akademik massal.'], 500);
        }
    }
}
