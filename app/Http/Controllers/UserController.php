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
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'student_id' => 'required|string',
            'major' => 'required|in:STI,BD,KWU',
            'batch_year' => 'required|integer',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
            'student_id' => $validated['student_id'],
            'major' => $validated['major'],
            'batch_year' => $validated['batch_year'],
        ]);

        return redirect()->back()->with('success', 'Student account created successfully!');
    }

    // 2. Simpan Admin Manual
    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'student_id' => null,
            'major' => null,
            'batch_year' => null,
        ]);

        return redirect()->back()->with('success', 'Admin account created successfully!');
    }

    // 3. Import Students dari CSV
    public function importStudents(Request $request)
    {
        // Validasi file
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048', // Max 2MB
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
            
            // Struktur CSV: name, email, password, student_id, major, batch_year
            // Pastikan ada 6 kolom
            if (count($row) < 6) {
                $errors[] = "Line $lineNumber: Incomplete data (requires 6 columns)";
                continue;
            }

            $email = trim($row[1]);
            $major = trim($row[4]);
            
            // Cek apakah email sudah ada
            if (User::where('email', $email)->exists()) {
                $errors[] = "Line $lineNumber: Email '$email' already exists";
                continue;
            }

            // Validasi major
            if (!in_array($major, ['STI', 'BD', 'KWU'])) {
                $errors[] = "Line $lineNumber: Invalid major '$major' (must be STI, BD, or KWU)";
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
                    'batch_year' => (int)trim($row[5]),
                ]);
                $count++;
            } catch (\Exception $e) {
                $errors[] = "Line $lineNumber: " . $e->getMessage();
                continue;
            }
        }

        fclose($handle);

        $message = "Successfully imported $count student account(s)";
        if (count($errors) > 0) {
            $message .= " with " . count($errors) . " error(s). ";
            $message .= "Errors: " . implode(', ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= " and " . (count($errors) - 5) . " more...";
            }
        } else {
            $message .= "!";
        }

        return back()->with('success', $message);
    }
}