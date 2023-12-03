<?php

namespace App\Http\Controllers\Roles;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        $roles = Role::all();
        return view('admin.roles', compact('roles'));
    }

    public function store(Request $request) {
        $role = new Role();
        $role->name = $request->name;
        $role->save();
        Session::flash('success', 'You have successfully added Role');
        return redirect()->back();
    }

    public function update(Request $request) {
        $editRole = Role::findOrFail($request->id);
        $editRole->name = $request->edit_name;
        $editRole->update();
        Session::flash('success', 'You have successfully updated the Role');
        return redirect()->back();
    }

    public function delete($id) {
        $delete = Role::findOrFail($id);
        $delete->delete();
        Session::flash('error', 'You have delete the Role');
        return redirect()->back();
    }
}
