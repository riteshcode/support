<?php

namespace App\Http\Controllers\Api;

use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use ApiHelper;


class RolesController extends Controller
{

    public function roles_all(Request $request){
        
        $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token,'role.view')){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }
        
        $roles_list = Role::with('permissions')
                    ->where('created_by',ApiHelper::get_adminid_from_token($api_token))
                    ->get();

        return ApiHelper::JSON_RESPONSE(true,$roles_list,'');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // Validate user page access
        $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token,'role.view')){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }
        
        // get all request val
        $current_page = !empty($request->page)?$request->page:1;
        $perPage = !empty($request->perPage)?$request->perPage:10;
        $search = $request->search;
        $sortBY = $request->sortBy;
        $ASCTYPE = $request->orderBY;


        $data_query = Role::with('permissions')->where('created_by',ApiHelper::get_adminid_from_token($api_token));
        
        // search
        if(!empty($search))
            $data_query = $data_query->where("name","LIKE", "%{$search}%");

        /* order by sorting */
        if(!empty($sortBY) && !empty($ASCTYPE)){
            $data_query = $data_query->orderBy($sortBY,$ASCTYPE);
        }else{
            $data_query = $data_query->orderBy('id','ASC');
        }
                    
        $skip = ($current_page == 1)?0:(int)($current_page-1)*$perPage;     // apply page logic
        
        $data_count = $data_query->count(); // get total count

        $data_list = $data_query->skip($skip)->take($perPage)->get();       // get pagination data

        $data_list = $data_list->map(function($data){
            $permissionListBox = [];
            if(!empty($data->permissions)){
                foreach($data->permissions as $key=>$per){
                    $permissionListBox[$key] = $per->name;
                }
            }
            $data->permissionList = implode("|", $permissionListBox);
            return $data;
        });

        $res = [
            'data'=>$data_list,
            'current_page'=>$current_page,
            'total_records'=>$data_count,
            'total_page'=>ceil((int)$data_count/(int)$perPage),
            'per_page'=>$perPage
        ];
        return ApiHelper::JSON_RESPONSE(true,$res,'');

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
        $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token,'role.create')){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }

        $user_id = ApiHelper::get_adminid_from_token($api_token);
        
        $role_name = $request->role_name.$user_id;
        $status = Role::where('name',$role_name)->first();
        if($status !== null){
            return ApiHelper::JSON_RESPONSE(false,[],'ROLE_EXISTS');
        }else{
            
            $role = Role::create([
                'name' => $role_name,
                'created_by' => $user_id,
                'display_name'=>$request->role_name
            ]);
            
            $role->givePermissionTo($request->permission_name);
            if($role)
                return ApiHelper::JSON_RESPONSE(true,[],'SUCCESS_ROLE_ADD');
            else
                return ApiHelper::JSON_RESPONSE(false,[],'ERROR_ROLE_ADD');

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
    public function edit(Request $request)
    {
        $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token,'role.edit')){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }

        $role_list = Role::with('permissions')->find($request->updateId);
        return ApiHelper::JSON_RESPONSE(true,$role_list,'');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token,'role.edit')){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }

        $user_id = ApiHelper::get_adminid_from_token($api_token);

        $role_name = $request->role_name.$user_id;

        $status = Role::where('name',$role_name)->first();

        if($status !== null){
            $status->syncPermissions($request->permission_name);
            return ApiHelper::JSON_RESPONSE(true,[],'ROLE_ALREADY_UPDATED');
        }else{
            Role::find($request->updatedId)->update([
                'name' => $role_name,
                'created_by' => $user_id,
                'display_name'=>$request->role_name
            ]);
            $role = Role::find($request->updatedId);
            $role->syncPermissions($request->permission_name);
            if($role)
                return ApiHelper::JSON_RESPONSE(true,[],'SUCCESS_ROLE_UPDATE');
            else
                return ApiHelper::JSON_RESPONSE(false,[],'ERROR_ROLE_UPDATE');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $api_token = $request->api_token;
        $id = $request->deleteId;

        if(!ApiHelper::is_page_access($api_token,'role.destroy')){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }
        $role = Role::find($id);
        $role->revokePermissionTo($role->permissions);
        $status = Role::destroy($id);
        if($status) {
            return ApiHelper::JSON_RESPONSE(true,[],'SUCCESS_ROLE_DELETE');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_ROLE_DELETE');
        }
    }
}
