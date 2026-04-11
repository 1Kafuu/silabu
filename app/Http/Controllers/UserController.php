<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


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

        $userName = User::where('name', $validated['name'])->first();

        if ($userName) {
            session()->flash('error', 'User with the same name already exists. Please try again.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 'active',
        ]);


        if ($user) {
            session()->flash('success', 'User created successfully!');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('user')
            ]);
        } else {
            session()->flash('error', 'Failed to create user. Please try again.');

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
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user')->with('success', 'User deleted successfully!');
    }

    public function edit($id)
    {
        $user = User::where('id', $id)->get();
        return view('admin.user.update-user', compact('user'));
    }

    public function manage($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $userRoles = RoleUser::where('iduser', $id)->get();
        return view('admin.user.manage-role-user', compact('user', 'roles', 'userRoles'));
    }

    

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = User::where('id', $id)->update($validated);

        if ($user) {
            session()->flash('success', 'User updated successfully!');

            $notificationHTML = view('components.notification')->render();

            Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('user')
            ]);
        } else {
            session()->flash('error', 'Failed to update user. Please try again.');

            $notificationHTML = view('components.notification')->render();

            Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }
    }

    public function assignRole(Request $request, $id)
    {
        $validated = $request->validate([
            'assigned_roles' => 'required|array',
            'assigned_roles.*' => 'required|integer|exists:role,idrole',
        ]);

        $assignedRoles = $request->input('assigned_roles', []);

        if (empty($assignedRoles)) {
            session()->flash('error', 'Please select at least one role.');

            $notificationHTML = view('components/notification')->render();

            Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }

        foreach ($assignedRoles as $roleId) {
            $existingRole = RoleUser::where('iduser', $id)
                ->where('idrole', $roleId)
                ->first();

            if (!$existingRole) {
                RoleUser::create([
                    'iduser' => $id,
                    'idrole' => $roleId,
                    'status' => 1,
                ]);
            }
        }

        session()->flash('success', 'Roles assigned successfully!');

        $notificationHTML = view('components/notification')->render();

        Log::info('Notification HTML: ' . $notificationHTML);

        return response()->json([
            'success' => true,
            'notification' => $notificationHTML,
            'redirect' => route('user')
        ]);
    }

    public function updateRoles(Request $request, $id)
    {
        $selectedRoles = $request->input('roles', []);
        $activeRole = $request->input('active_role');

        // Get current user roles
        $currentUserRoles = RoleUser::where('iduser', $id)->get();
        $currentRoleIds = $currentUserRoles->pluck('idrole')->toArray();

        // Roles to add (in selectedRoles but not in currentRoleIds)
        $rolesToAdd = array_diff($selectedRoles, $currentRoleIds);

        // Roles to remove (in currentRoleIds but not in selectedRoles)
        $rolesToRemove = array_diff($currentRoleIds, $selectedRoles);

        // Add new roles
        foreach ($rolesToAdd as $roleId) {
            RoleUser::create([
                'iduser' => $id,
                'idrole' => $roleId,
                'status' => ($activeRole == $roleId) ? 1 : 0,
            ]);
        }

        // Remove unselected roles
        RoleUser::where('iduser', $id)->whereIn('idrole', $rolesToRemove)->delete();

        // Update active role
        if ($activeRole && in_array($activeRole, $selectedRoles)) {
            RoleUser::where('iduser', $id)->update(['status' => 0]);
            RoleUser::where('iduser', $id)
                ->where('idrole', $activeRole)
                ->update(['status' => 1]);
        }

        session()->flash('success', 'Roles updated successfully!');

        $notificationHTML = view('components/notification')->render();

        Log::info('Notification HTML: ' . $notificationHTML);

        return response()->json([
            'success' => true,
            'notification' => $notificationHTML,
            'redirect' => route('user')
        ]);
    }

    public function setActiveRole($userId, $roleUserId)
    {
        RoleUser::where('iduser', $userId)->update(['status' => 0]);
        
        $roleUser = RoleUser::findOrFail($roleUserId);
        $roleUser->update(['status' => 1]);

        session()->flash('success', 'Active role set successfully!');

        $notificationHTML = view('components.notification')->render();

        Log::info('Notification HTML: ' . $notificationHTML);

        return response()->json([
            'success' => true,
            'notification' => $notificationHTML
        ]);
    }

    public function removeRole($userId, $roleUserId)
    {
        $roleUser = RoleUser::findOrFail($roleUserId);
        
        $wasActive = $roleUser->status == 1;
        $roleUser->delete();

        if ($wasActive) {
            $newActiveRole = RoleUser::where('iduser', $userId)->first();
            if ($newActiveRole) {
                $newActiveRole->update(['status' => 'active']);
            }
        }

        session()->flash('success', 'Role removed successfully!');

        $notificationHTML = view('components.notification')->render();

        Log::info('Notification HTML: ' . $notificationHTML);

        return response()->json([
            'success' => true,
            'notification' => $notificationHTML
        ]);
    }

    public function setInactiveRole($userId, $roleUserId)
    {
        $roleUser = RoleUser::findOrFail($roleUserId);
        $roleUser->update(['status' => 0]);

        session()->flash('success', 'Role set as inactive!');

        $notificationHTML = view('components/notification')->render();

        Log::info('Notification HTML: ' . $notificationHTML);

        return response()->json([
            'success' => true,
            'notification' => $notificationHTML
        ]);
    }
}