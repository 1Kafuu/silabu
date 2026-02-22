<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index()
    {
        $users = User::whereNull('deleted_at')->get();
        return view('admin.user.user', compact('users'));
    }
    public function create()
    {
        return view('admin.user.create-user');
    }

    public function store(Request $request)
    {
        // Validasi data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 'active',
        ]);

        if ($user) {
            return redirect()->route('user')->with('success', 'User created successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to create user!');
        }
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user')->with('success', 'User deleted successfully!');
    }

    public function edit($id) {
        $user = User::where('id', $id)->get();
        return view('admin.user.update-user', compact('user'));
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = User::where('id', $id)->update($validated);
        return redirect()->route('user')->with('success', 'User updated successfully!');
    }
}