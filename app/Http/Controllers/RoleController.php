<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function index() {
        $roles = Role::all();
        return view('admin.role.role', compact('roles'));
    }

    public function create() {
        return view('admin.role.create-role');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_role' => 'required|string|max:255',
        ]);

        $role = Role::where('nama_role', $validated['nama_role'])->first();

        if ($role) {
            session()->flash('error', 'Role with the same name already exists. Please try again.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }

        $result = Role::create($validated);

        if ($result) {
            session()->flash('success', 'Role created successfully!');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('role')
            ]);
        } else {
            session()->flash('error', 'Failed to create role. Please try again.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }
    }

    public function delete($id)
    {
        $role = Role::where('idrole', $id)->firstOrFail();
        $role->delete();

        return redirect()->route('role')->with('success', 'Role deleted successfully!');
    }

    public function edit($id) {
        $role = Role::where('idrole', $id)->first();
        return view('admin.role.update-role', compact('role'));
    }

    public function update($id, Request $request)
    {
        $validated = $request->validate([
            'nama_role' => 'required|string|max:255',
        ]);

        $role = Role::where('idrole', $id)->update($validated);

        if ($role) {
            session()->flash('success', 'Role updated successfully!');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('role')
            ]);
        } else {
            session()->flash('error', 'Failed to update role. Please try again.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }
    }
}
