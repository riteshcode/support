<?php

namespace Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ApiHelper;
use App\Models\User;
use Modules\HRM\Models\Designation;
use Illuminate\Support\Str;
use Validator;
use Auth;
class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $DesignationView = 'designation-view';
    public $DesignationManage = 'designation-manage';
    public $DesignationDelete = 'designation-delete';

    public function index_all(Request $request){

        $api_token = $request->api_token;

        $designation_list = Designation::where('created_by',ApiHelper::get_adminid_from_token($api_token))
            ->get();

        return ApiHelper::JSON_RESPONSE(true,$designation_list,'');
    }

    public function index(Request $request){

        // Validate user page access
        $api_token = $request->api_token;

        if(!ApiHelper::is_page_access($api_token, $this->DesignationView)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }

        $current_page = !empty($request->page)?$request->page:1;
        $perPage = !empty($request->perPage)?$request->perPage:10;
        $search = $request->search;
        $sortBY = $request->sortBy;
        $ASCTYPE = $request->orderBY;

        $data_query = Designation::where('created_by',ApiHelper::get_adminid_from_token($api_token));

        // search
        if(!empty($search))
            $data_query = $data_query->where("designation","LIKE", "%{$search}%");

        /* order by sorting */
        if(!empty($sortBY) && !empty($ASCTYPE)){
            $data_query = $data_query->orderBy($sortBY,$ASCTYPE);
        }else{
            $data_query = $data_query->orderBy('designation_id','ASC');
        }

        $skip = ($current_page == 1)?0:(int)($current_page-1)*$perPage;     // apply page logic

        $data_count = $data_query->count(); // get total count

        $data_list = $data_query->skip($skip)->take($perPage)->get(); 
        
        $data_list = $data_query->get();

        $data_list = $data_list->map(function($data){

            // $user = User::where('id',$data->created_by)->first();
            $data->created_by = $data->user->first_name;
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

    public function store(Request $request)
    {
        $api_token = $request->api_token;

        if(!ApiHelper::is_page_access($api_token,$this->DesignationManage))
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');

        $validator = Validator::make($request->all(),[
            'designation' => 'required',
        ],[
            'designation.required'=>'DESIGNATION_REQUIRED',
            // 'designation.unique'=>'DESIGNATION_UNIQUE',
        ]);
        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());
        

        $user_id = ApiHelper::get_adminid_from_token($api_token);

        $Insert = $request->only('designation');
        $Insert['created_by'] = $user_id;

        $res = Designation::create($Insert);
        if($res)
            return ApiHelper::JSON_RESPONSE(true,$res->id,'DESIGNATION_CREATED');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'UNABLE_DESIGNATION_CREATED');

    }

    public function edit(Request $request)
    {
        $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token,$this->DesignationManage)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }

        $data_list = Designation::where('designation_id',$request->designation_id)->first();
        return ApiHelper::JSON_RESPONSE(true,$data_list,'');

    }

    public function update(Request $request)
    {
        $api_token = $request->api_token;
        $designation_id = $request->designation_id;

        if(!ApiHelper::is_page_access($api_token,$this->DesignationManage)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }

        $user_id = Auth::id();

        $validator = Validator::make($request->all(),[
            'designation' => 'required|unique:sa_designations',
        ],[
            'designation.required'=>'DESIGNATION_REQUIRED',
            'designation.unique'=>'DESIGNATION_UNIQUE',
        ]);
        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());
        

        $user_id = ApiHelper::get_adminid_from_token($api_token);

        $Insert = $request->only('designation');

        $res = Designation::where('designation_id',$designation_id)->update($Insert);

        if($res)
            return ApiHelper::JSON_RESPONSE(true,$res,'DESIGNATION_UPDATED');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'UNABLE_DESIGNATION_UPDATED');
    }

    public function destroy(Request $request)
    {
        $api_token = $request->api_token;
        $designation_id = $request->designation_id;

        if(!ApiHelper::is_page_access($api_token,$this->DesignationDelete)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }
        $status = Designation::where('designation_id',$designation_id)->delete();
        if($status) {
            return ApiHelper::JSON_RESPONSE(true,[],'DESIGNATION_DELETED');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'NOT_DESIGNATION_DELETED');
        }
    }

}
