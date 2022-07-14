<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;
use Helper;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // check user has access to this file
        if(!Helper::is_user_access('role')){
            return redirect('/');
        }

        $roleList = Role::where('created_by',Helper::get_parent_id())->get();
        $permissionList = Permission::all();
        return view('admin.role.index',compact('roleList','permissionList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role_name = $request->role_name.Auth::id();
        $status = Role::where('name',$role_name)->first();
        if($status !== null){
            return redirect()->back()->with('warning',"Already this role exist ! !");
        }else{
            
            $role = Role::create([
                'name' => $request->role_name.Auth::id(),
                'created_by' => Auth::id(),
                'display_name'=>$request->role_name
            ]);
            
            $role->givePermissionTo($request->permission_name);

            if($role)
                return redirect()->back()->with('success',"Role created");
            else
                return redirect()->back()->with('warning',"Unable to create role !");

        }
            
        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role_name = $request->role_name.Auth::id();

        $status = Role::where('name',$role_name)->first();

        if($status !== null){
            $status->syncPermissions($request->permission_name);
            return redirect()->back()->with('success',"Already updated role exist ! Sync Permissions done !");
        }else{
            
            $role = Role::find($id)->update([
                'name' => $role_name,
                'created_by' => Auth::id(),
                'display_name'=>$request->role_name
            ]);
            
            $role->syncPermissions($request->permission_name);

            if($role)
                return redirect()->back()->with('success',"Role updated");
            else
                return redirect()->back()->with('warning',"Unable to update role !");

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        $role->revokePermissionTo($role->permissions);
        // dd($role);
        $status = Role::destroy($id);

        if ($status) {
            return back()->with('success', 'Role deleted successfully');
        }else{
            return back()->with('warning', 'Not able to delete !');
        }
    }
}
