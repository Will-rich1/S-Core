<?php

namespace App\Http\Controllers;

use App\Models\User;
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
            'password' => 'nullable|string|min:6'
        ]);

        // Support both internal primary-key id and student_id passed from UI
        $user = User::find($id);
        if (!$user) {
            // try lookup by student_id (students use student_id as external identifier)
            $user = User::where('student_id', $id)->firstOrFail();
        }

        // If password provided, use it; otherwise generate a random one
        $provided = $request->input('password');
        if ($provided && strlen(trim($provided)) >= 6) {
            $newPassword = $provided;
            $generated = false;
        } else {
            $newPassword = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789'), 0, 10);
            $generated = true;
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        Log::info('resetPassword saved', ['admin_id' => Auth::id(), 'target_user' => $user->id, 'generated' => $generated]);

        // Log activity (best-effort)
        try {
            $now = now();
            if (Schema::hasColumn('activity_logs', 'entity_type')) {
                DB::table('activity_logs')->insert([
                    'user_id' => Auth::id(),
                    'action' => 'admin_reset_password',
                    'entity_type' => 'user',
                    'entity_id' => $user->id,
                    'details' => json_encode(['generated' => $generated ? true : false]),
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
                    'message' => json_encode(['generated' => $generated ? true : false]),
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            } else {
                DB::table('activity_logs')->insert([
                    'user_id' => Auth::id(),
                    'action' => 'admin_reset_password',
                    'details' => json_encode(['generated' => $generated ? true : false]),
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to insert activity_log for resetPassword: '.$e->getMessage());
        }

        return response()->json([
            'message' => 'Password reset successfully',
            'generated' => $generated,
            'password' => $generated ? $newPassword : null
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
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');

        if ($handle === false) {
            return back()->withErrors(['csv_file' => 'Failed to read CSV file.']);
        }

        $count = 0;
        $errors = [];
        $lineNumber = 0;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $lineNumber++;
            
            // Skip header row if exists (optional logic, simple check if row[0] is 'name')
            if ($lineNumber == 1 && strtolower(trim($row[0])) == 'name') {
                continue;
            }

            // Struktur CSV: name, email, password, student_id, major, batch_year
            if (count($row) < 6) {
                $errors[] = "Line $lineNumber: Incomplete data";
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
                User::create([
                    'name'       => trim($row[0]),
                    'email'      => $email,
                    'password'   => Hash::make(trim($row[2])),
                    'role'       => 'student',
                    'student_id' => trim($row[3]),
                    'major'      => $major,
                    'year'       => (int)trim($row[5]), // Map ke 'year'
                ]);
                $count++;
            } catch (\Exception $e) {
                $errors[] = "Line $lineNumber: " . $e->getMessage();
                continue;
            }
        }

        fclose($handle);

        $message = "Successfully imported $count student(s).";
        if (count($errors) > 0) {
            $message .= " Errors: " . implode(', ', array_slice($errors, 0, 3)) . (count($errors) > 3 ? "..." : "");
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
}
