<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index(Request $request)
    {
        // Get all users with their roles
        $users = User::with('role')->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'data' => $users
        ]);
    }

    /**
     * Update the specified user's role.
     */
    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        // Optional: Prevent changing the last admin's role to Customer
        if ($user->role_id == 1 && $validated['role_id'] == 2) {
            $adminCount = User::where('role_id', 1)->count();
            if ($adminCount <= 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot change role of the only remaining administrator.'
                ], 422);
            }
        }

        $user->update(['role_id' => $validated['role_id']]);

        // Load role for the response
        $user->load('role');

        return response()->json([
            'status' => true,
            'message' => 'User role updated successfully.',
            'data' => $user
        ]);
    }
}
