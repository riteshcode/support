<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;
use Helper;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // check user has access to this file
        if(!Helper::is_user_access('permission')){
            return redirect('/');
        }

        $permissionList = Permission::all();
        return view('admin.permission.index',compact('permissionList'));
        
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
        $fail = 'permission already exist [ ';
        $success = 'permission created list [ ';

        $permission_list = explode(',',$request->permission_name);

        foreach ($permission_list as $key => $permission_name) {
            $status = Permission::where('name',$permission_name)->first();
            if($status !== null)
                $fail .= $permission_name.','; 
                
            else{
                
                $role = Permission::create(['name' => $permission_name ]);
                if($role)
                    $success .= $permission_name.',';

            }
        }

        return redirect()->back()->with('success',$success.' ] And '.$fail.' ]');
            
        
        
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
        $status = Permission::where('name',$request->permission_name)->first();

        if($status !== null){
            return redirect()->back()->with('warning',"Already updated permission exist ! !");
        }else{
            $res = Permission::find($id)->update([ 'name' => $request->permission_name ]);
            if($res)
                return redirect()->back()->with('success',"Permission updated");
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
        $status = Permission::destroy($id);
        
        if ($status) {
            return back()->with('success', 'Deleted successfully');
        }else{
            return back()->with('warning', 'Not able to delete !');
        }
    }
}
