<?php

namespace Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ApiHelper;
use Modules\HRM\Models\Department;
use Modules\HRM\Models\LeaveType;
use Modules\HRM\Models\Role;
use Modules\HRM\Models\RoleToPermission;
use Illuminate\Support\Str;
use Validator;


class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index(Request $request){
        $api_token = $request->api_token;
        $list = LeaveType::all();
        return ApiHelper::JSON_RESPONSE(true,$list,'');
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

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'info' => 'required',
            'no_of_days' => 'required',
        ],[
            'name.required'=>'LEAVE_TYPE_NAME_REQUIRED',
            'info.required'=>'LEAVE_TYPE_INFO_REQUIRED',
            'no_of_days.required'=>'NO_OF_DAYS_REQUIRED',
        ]);

        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());
        

        $user_id = ApiHelper::get_adminid_from_token($api_token);

        $Insert = $request->only('name','info','no_of_days','max_allowed');
        // $Insert['created_by'] = $user_id;

        $res = LeaveType::create($Insert);
        if($res)
            return ApiHelper::JSON_RESPONSE(true,$res->id,'LEAVE_TYPE_CREATED');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'UNABLE_LEAVE_TYPE_CREATED');

    

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
        $data_list = LeaveType::where('leave_type_id',$request->updateId)->first();
        return ApiHelper::JSON_RESPONSE(true,$data_list,'');

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
        $updateId = $request->updateId;

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'info' => 'required',
            'no_of_days' => 'required',
        ],[
            'name.required'=>'LEAVE_TYPE_NAME_REQUIRED',
            'info.required'=>'LEAVE_TYPE_INFO_REQUIRED',
            'no_of_days.required'=>'NO_OF_DAYS_REQUIRED',
        ]);

        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());
        

        $user_id = ApiHelper::get_adminid_from_token($api_token);

        $Insert = $request->only('name','info','no_of_days','max_allowed');
        
        $res = LeaveType::where('leave_type_id', $updateId)->update($Insert);
        if($res)
            return ApiHelper::JSON_RESPONSE(true,$res,'LEAVE_TYPE_UPDATED');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'UNABLE_LEAVE_TYPE_UPDATED');

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

        $status = LeaveType::where('leave_type_id',$id)->delete();
        if($status) {
            return ApiHelper::JSON_RESPONSE(true,[],'LEAVE_TYPE_DELETED');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'NOT_LEAVE_TYPE_DELETED');
        }
    }
}
