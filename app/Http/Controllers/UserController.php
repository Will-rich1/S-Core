<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
}