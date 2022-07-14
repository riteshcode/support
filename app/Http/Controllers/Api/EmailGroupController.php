<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use ApiHelper;
use App\Models\EmailGroup;
use App\Models\Menu;

class EmailGroupController extends Controller
{

    public function index(Request $request){

        // Validate user page access
        $api_token = $request->api_token;

        $data_list = EmailGroup::all();

        $res = [
            'data_list'=> $data_list
        ];
        return ApiHelper::JSON_RESPONSE(true,$res,'');
    }


    public function store(Request $request)
    {
        $api_token = $request->api_token;


        $validator = Validator::make($request->all(),[
            'group_name' => 'required',
        ],[
            'group_name.required'=>'GROUP_NAME_REQUIRED',        
        ]);
        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());

        $banner_data=$request->only(['group_name']);

        
        $data = EmailGroup::create($banner_data);

        if($data)
            return ApiHelper::JSON_RESPONSE(true,$data,'SUCCESS_EMAIL_GROUP_ADD');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_EMAIL_GROUP_ADD');

    }

    public function edit(Request $request)
    {
        // return ApiHelper::JSON_RESPONSE(true,$request->all(),'');
        $api_token = $request->api_token;
        $data_list = EmailGroup::find($request->group_id);
        return ApiHelper::JSON_RESPONSE(true,$data_list,'');

    }

    public function update(Request $request)
    { 

        $api_token = $request->api_token;

        $validator = Validator::make($request->all(),[
            'group_name' => 'required',
        ],[
            'group_name.required'=>'GROUP_NAME_REQUIRED',                   
        ]);

        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());

        $EmailGroup_update_data=$request->only(['group_name']);
        $data = EmailGroup::where('group_id', $request->group_id)->update($EmailGroup_update_data);

        if($data)
            return ApiHelper::JSON_RESPONSE(true,$data,'SUCCESS_EMAIL_GROUP_UPDATE');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_EMAIL_GROUP_UPDATE');
    }


    public function destroy(Request $request)
    {
        $api_token = $request->api_token;

        $status = EmailGroup::where('group_id',$request->group_id)->delete();
        if($status) {
            return ApiHelper::JSON_RESPONSE(true,[],'SUCCESS_EMAIL_GROUP_DELETE');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_EMAIL_GROUP_DELETE');
        }
    }

    public function changeStatus(Request $request)
    {

        $api_token = $request->api_token; 
        $group_id = $request->group_id;
        $sub_data = EmailGroup::find($group_id);
        $sub_data->status = ($sub_data->status == 0 ) ? 1 : 0;         
        $sub_data->save();
        
        return ApiHelper::JSON_RESPONSE(true,$sub_data,'SUCCESS_STATUS_UPDATE');
    }


}
