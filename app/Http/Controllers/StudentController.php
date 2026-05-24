<?php

namespace App\Http\Controllers;

use App\Models\NFC;
use App\Models\RoleUser;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    const STUDENT_ROLE_ID = 15;

    public function index()
    {
        $students = Student::with('user', 'nfc')->get();
        return view('admin.student.student', compact('students'));
    }

    public function create()
    {
        return view('admin.student.create-student');
    }

    public function store(Request $request)
    {   
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:8|confirmed',
            'NIM'           => 'required|integer|digits:9|unique:students,NIM',
            'fakultas'      => 'required|string|max:255',
            'prodi'         => 'required|string|max:255',
            'serial_number' => 'required|string|unique:nfc_cards,serial_number',
        ]);

        try {
            DB::beginTransaction();

            // 1. Create user
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'status'   => 'active',
            ]);

            // 2. Create student
            $student = Student::create([
                'user_id'  => $user->id,
                'NIM'      => $validated['NIM'],
                'fakultas' => $validated['fakultas'],
                'prodi'    => $validated['prodi'],
            ]);

            // 3. Create NFC card
            NFC::create([
                'student_id'    => $student->id,
                'serial_number' => $validated['serial_number'],
            ]);

            // 4. Assign student role
            RoleUser::create([
                'iduser' => $user->id,
                'idrole' => self::STUDENT_ROLE_ID,
                'status' => 1,
            ]);

            DB::commit();

            session()->flash('success', 'Student created successfully!');
            $notificationHTML = view('components.notification')->render();
            Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success'      => true,
                'notification' => $notificationHTML,
                'redirect'     => route('student-list'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create student: ' . $e->getMessage());

            session()->flash('error', 'Failed to create student. Please try again.');
            $notificationHTML = view('components.notification')->render();

            return response()->json([
                'success'      => false,
                'notification' => $notificationHTML,
            ], 500);
        }
    }

    public function edit($id)
    {
        $student = Student::with('user')->findOrFail($id);
        return view('admin.student.edit-student', compact('student'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'NIM'      => 'required|integer|digits:9|unique:students,NIM,' . $id,
            'fakultas' => 'required|string|max:255',
            'prodi'    => 'required|string|max:255',
        ]);

        $student = Student::findOrFail($id);
        $student->update($validated);

        session()->flash('success', 'Student updated successfully!');
        $notificationHTML = view('components.notification')->render();
        Log::info('Notification HTML: ' . $notificationHTML);

        return response()->json([
            'success'      => true,
            'notification' => $notificationHTML,
            'redirect'     => route('student-list'),
        ]);
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $student = Student::with('user')->findOrFail($id);
            $userId  = $student->user_id;

            // Delete student (cascades to nfc_cards and attendances)
            $student->delete();

            // Remove student role from user
            RoleUser::where('iduser', $userId)
                ->where('idrole', self::STUDENT_ROLE_ID)
                ->delete();

            // Soft delete user
            User::findOrFail($userId)->delete();

            DB::commit();

            return redirect()->route('student-list')->with('success', 'Student deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete student: ' . $e->getMessage());

            return redirect()->route('student-list')->with('error', 'Failed to delete student. Please try again.');
        }
    }
}
