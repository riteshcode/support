<?php

namespace Modules\Department\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ApiHelper;
use Modules\Department\Models\Role;
use Modules\Department\Models\RoleToPermission;
use Illuminate\Support\Str;


class RolesController extends Controller
{
    public $DepartmentView = 'department-view';
    public $DepartmentManage = 'department-manage';
    public $DepartmentDelete = 'department-delete';

    public function roles_all(Request $request){

        $api_token = $request->api_token;

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

        if(!ApiHelper::is_page_access($api_token, $this->DepartmentView)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }

        // get all request val
        $current_page = !empty($request->page)?$request->page:1;
        $perPage = !empty($request->perPage)?$request->perPage:10;
        $search = $request->search;
        $sortBY = $request->sortBy;
        $ASCTYPE = $request->orderBY;


        $data_query = Role::where('created_by',ApiHelper::get_adminid_from_token($api_token));

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

        $data_list = $data_query->skip($skip)->take($perPage)->get();  
             // get pagination data

        
        $data_list = $data_list->map(function($data){
            $permissionListBox = [];
            if(!empty($data->permissions)){
                foreach($data->permissions as $key=>$per){
                    $permissionListBox[$key] = $per->name;
                }
            }
            $data->permissionList = implode(",", $permissionListBox);
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
        if(!ApiHelper::is_page_access($api_token,$this->DepartmentManage)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }

        $user_id = ApiHelper::get_adminid_from_token($api_token);

        $role_name = $request->role_name.$user_id;
        $status = Role::where('name',$role_name)->first();
        
        // return ApiHelper::JSON_RESPONSE(true,$status,'ROLE_CREATED');
            
        if($status !== null){
            return ApiHelper::JSON_RESPONSE(false,[],'ROLE_EXISTS');
        }else{

            $Insert = [
                'name' => $role_name,
                'created_by' => $user_id,
                'display_name'=>$request->role_name,
                'slug'=>Str::slug($request->role_name)
            ];

            $role = Role::create($Insert);
            
            // $role->givePermissionTo($request->permission_name);
            $permissions  = $request->permission_name;

            if(sizeof($permissions) > 0 ){
                foreach ($permissions as $key => $permission_id) {
                    RoleToPermission::updateOrCreate([
                        'permission_id'=>$permission_id,
                        'role_id'=>$role->id,
                    ],[
                        'permission_id'=>$permission_id,
                        'role_id'=>$role->id,
                    ]);
                }
            }


            if($role)
                return ApiHelper::JSON_RESPONSE(true,$role->id,'ROLE_CREATED');
            else
                return ApiHelper::JSON_RESPONSE(false,[],'UNABLE_CREATE_ROLE');

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
        if(!ApiHelper::is_page_access($api_token,$this->DepartmentManage)){
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
        if(!ApiHelper::is_page_access($api_token,$this->DepartmentManage)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }

        $user_id = ApiHelper::get_adminid_from_token($api_token);

        $role_name = $request->role_name.$user_id;

        $status = Role::where('name',$role_name)->first();

        if($status !== null){
            $status->permissions()->detach();

            $permissions  = $request->permission_name;
            $status->permissions()->attach($permissions);
            
            if(sizeof($permissions) > 0 ){
                // foreach ($permissions as $key => $permission_id) {
                //     RoleToPermission::updateOrCreate(
                //         ['permission_id'=>$permission_id,'role_id'=>$status->id],
                //         ['permission_id'=>$permission_id,'role_id'=>$status->id]
                //     );
                // }
            }
            return ApiHelper::JSON_RESPONSE(true,[],'ROLE_UPDATED_ALREADY');
        }else{
            Role::find($request->updatedId)->update([
                'name' => $role_name,
                'created_by' => $user_id,
                'display_name'=>$request->role_name,
                'slug'=>Str::slug($request->role_name)
            ]);
            $role = Role::find($request->updatedId);
            
            $permissions  = $request->permission_name;

            if(sizeof($permissions) > 0 ){
                foreach ($permissions as $key => $permission_id) {
                    RoleToPermission::updateOrCreate([
                        'permission_id'=>$permission_id,
                        'role_id'=>$role->id,
                    ],[
                        'permission_id'=>$permission_id,
                        'role_id'=>$role->id,
                    ]);
                }
            }

            if($role)
                return ApiHelper::JSON_RESPONSE(true,[],'ROLE_UPDATED');
            else
                return ApiHelper::JSON_RESPONSE(false,[],'UNABLE_UPDATE_ROLE');
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

        if(!ApiHelper::is_page_access($api_token,$this->DepartmentDelete)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }
        $role = Role::find($id);
        $role->permissions()->detach();
        $status = Role::destroy($id);
        if($status) {
            return ApiHelper::JSON_RESPONSE(true,[],'ROLE_DELETED');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'NOT_DELETED_ROLE');
        }
    }
}
