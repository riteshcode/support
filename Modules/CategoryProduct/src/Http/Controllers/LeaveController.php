<?php

namespace Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ApiHelper;
use Modules\HRM\Models\Department;
use Modules\HRM\Models\Leave;
use Modules\HRM\Models\Role;
use Modules\HRM\Models\RoleToPermission;
use Illuminate\Support\Str;
use Validator;

use DateTime;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public $DepartmentView = 'department-view';
    public $DepartmentManage = 'department-manage';
    public $DepartmentDelete = 'department-delete';
    
    public function index(Request $request)
    {

        // Validate user page access
        $api_token = $request->api_token;

        // get all request val
        $current_page = !empty($request->page)?$request->page:1;
        $perPage = !empty($request->perPage)?$request->perPage:10;
        $search = $request->search;
        $sortBY = $request->sortBy;
        $ASCTYPE = $request->orderBY;

        $role_name = ApiHelper::get_role_from_token($api_token);
        if($role_name == 'admin' || $role_name == 'superadmin')
           $data_query = Leave::query();
        else
           $data_query = Leave::where('user_id',ApiHelper::get_user_id_from_token($api_token));


        // search
        if(!empty($search))
            $data_query = $data_query->where("subject","LIKE", "%{$search}%");

        /* order by sorting */
        if(!empty($sortBY) && !empty($ASCTYPE)){
            $data_query = $data_query->orderBy($sortBY,$ASCTYPE);
        }else{
            $data_query = $data_query->orderBy('leave_id','ASC');
        }

        $skip = ($current_page == 1)?0:(int)($current_page-1)*$perPage;     // apply page logic

        $data_count = $data_query->count(); // get total count

        $data_list = $data_query->skip($skip)->take($perPage)->get();  
             // get pagination data
        if (!empty($data_list)) { 
            $data_list->map(function($data){
                $data->status = ($data->status == "0")?'Pending':(
                    ($data->status == "1")?'Approved':'Rejected'
                );
                return $data;
            });
        }

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

        $validator = Validator::make($request->all(),[
            'leave_type_id' => 'required',
            'subject' => 'required',
            'reason' => 'required',
            'from_date'=>'required',
            'to_date'=>'required',
        ],[
            'leave_type_id.required'=>'LEAVE_TYPE_REQUIRED',
            'subject.unique'=>'LEAVE_SUBJECT_REQUIRED',
            'reason.required'=>'LEAVE_REASON_REQUIRED',
            'from_date.required'=>'LEAVE_FROMDATE_REQUIRED',
            'to_date.required'=>'LEAVE_TODATE_REQUIRED',
        ]);
        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());
        
        $Insert = $request->only('leave_type_id','subject','reason','from_date','to_date');
        
        $from_date = new DateTime($request->from_date);
        $to_date = new DateTime($request->to_date);
        $interval = $to_date->diff($from_date);

        $Insert['total_leave_days'] = (int)$interval->format('%a');
        $Insert['user_id'] = ApiHelper::get_user_id_from_token($api_token);
        $res = Leave::create($Insert);
        if($res)
            return ApiHelper::JSON_RESPONSE(true,$res->id,'LEAVE_SUBMIITED');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'UNABLE_LEAVE_SUBMIITED');

    

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

        $data_list = Department::where('leave_id',$request->updateId)->first();
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

        if(!ApiHelper::is_page_access($api_token,$this->DepartmentManage)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }

        $user_id = ApiHelper::get_adminid_from_token($api_token);

        $validator = Validator::make($request->all(),[
            'dept_name' => 'required',
            'dept_details' => 'required',
            'status' => 'required',
        ],[
            'dept_name.required'=>'DEPARTMENT_NAME_REQUIRED',
            'dept_details.required'=>'DEPARTMENT_DETAILS_REQUIRED',
            'status.required'=>'DEPARTMENT_STATUS_REQUIRED',
        ]);
        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());
        

        $user_id = ApiHelper::get_adminid_from_token($api_token);

        $Insert = $request->only('dept_name','dept_details','status');

        $res = Department::where('leave_id',$updateId)->update($Insert);

        if($res)
            return ApiHelper::JSON_RESPONSE(true,$res,'DEPARTMENT_UPDATED');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'UNABLE_DEPARTMENT_UPDATED');
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
        $status = Department::where('leave_id',$id)->delete();
        if($status) {
            return ApiHelper::JSON_RESPONSE(true,[],'DEPARTMENT_DELETED');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'NOT_DEPARTMENT_DELETED');
        }
    }
    public function statusChange(Request $request){
        $api_token = $request->api_token;
        $id = $request->updateId;
        $status = $request->status;
        $res = Leave::where('leave_id', $id)->update(['status'=>$status]);
        $msg = ($status == '1')? 'APPROVED' : 'REJECTED';
        
        // generate notification
        $leave = Leave::where('leave_id',$id)->first();
        $c_msg = "Your Leave $msg from ".$leave->from_date.' To '. $leave->to_date;
        $title = 'LEAVE_'.$msg;
        $gendata = [ "user_id" => $leave->user_id, "type" => 1, "title" =>$title , "msg" =>$c_msg  ];
        ApiHelper::generate_notification($gendata);

        return ApiHelper::JSON_RESPONSE(true,$res,'LEAVE_'.$msg);
    }
}
