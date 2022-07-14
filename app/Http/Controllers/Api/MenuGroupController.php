<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use ApiHelper;
use App\Models\MenuGroup;
use App\Models\Menu;



class MenuGroupController extends Controller
{

    public function index(Request $request){

        // Validate user page access
        $api_token = $request->api_token;

        $data_list = MenuGroup::all();

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
            'group_position' => 'required',
        ],[
            'group_name.required'=>'GROUP_NAME_REQUIRED',
            'group_position.required'=>'GROUP_POSITION_REQUIRED',            
        ]);
        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());

        $banner_data=$request->except(['api_token']);

        
        $data = MenuGroup::create($banner_data);

        if($data)
            return ApiHelper::JSON_RESPONSE(true,$data,'SUCCESS_MENU_GROUP_ADD');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_MENU_GROUP_ADD');

    }

    public function edit(Request $request)
    {
        // return ApiHelper::JSON_RESPONSE(true,$request->all(),'');
        $api_token = $request->api_token;
        
        $data_list = MenuGroup::where('group_id',$request->group_id)->first();
        return ApiHelper::JSON_RESPONSE(true,$data_list,'');

    }

    public function update(Request $request)
    { 

        $api_token = $request->api_token;

        $validator = Validator::make($request->all(),[
            'group_name' => 'required',
            'group_position' => 'required',
        ],[
            'group_name.required'=>'GROUP_NAME_REQUIRED',
            'group_position.required'=>'GROUP_POSITION_REQUIRED',                      
        ]);

        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());

        $menugroup_update_data=$request->except(['api_token','group_id']);
        $data = MenuGroup::where('group_id', $request->group_id)->update($menugroup_update_data);

        if($data)
            return ApiHelper::JSON_RESPONSE(true,$data,'SUCCESS_MENU_GROUP_UPDATE');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_MENU_GROUP_UPDATE');
    }


    public function destroy(Request $request)
    {
        $api_token = $request->api_token;

        $status = MenuGroup::where('group_id',$request->group_id)->delete();
        if($status) {
            return ApiHelper::JSON_RESPONSE(true,[],'SUCCESS_MENU_GROUP_DELETE');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_MENU_GROUP_DELETE');
        }
    }

    public function changeStatus(Request $request)
    {

        $api_token = $request->api_token; 
        $group_id = $request->group_id;
        $sub_data = MenuGroup::find($group_id);
        $sub_data->status = ($sub_data->status == 0 ) ? 1 : 0;         
        $sub_data->save();
        
        return ApiHelper::JSON_RESPONSE(true,$sub_data,'SUCCESS_STATUS_UPDATE');
    }


}
