<?php

namespace App\Http\Controllers\UserManagement;
use App\Http\Controllers\Controller;

use App\Models\Role;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;


use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.users.index');
    }

    public function addUsers()
    {
        $roles = Role::all();
        return view('admin.users.addUsers', compact('roles'));
    }
    public function editUsers(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|min:8|confirmed',
                'password_confirmation' => 'required|min:6',
                'role' => 'required',
            ]
        );
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role_id = $request->role;
            $user->save();
            Session::flash('success', 'User created successfully');
            return redirect()->route('admin.users');
    }

    public function update(Request $request, User $user)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'password' => 'required|min:8|confirmed',
                'password_confirmation' => 'required|min:6',
                'role' => 'required',
            ]
        );
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role_id = $request->role;
            $user->update();
            Session::flash('success', 'User updated successfully');
            return redirect()->route('admin.users');
    }

    public function delete(User $user)
    {
        $user->is_active=0;
        $user->update();
        Session::flash('error', 'User deleted successfully');
        return redirect()->route('admin.users');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }
}
