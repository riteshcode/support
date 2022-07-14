<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use ApiHelper;
use App\Models\Menu;
use App\Models\WebPages;
use Modules\Ecommerce\Models\Category;


class MenuController extends Controller
{

    public function list(Request $request){


        $api_token = $request->api_token;
        $data_list = Menu::where('group_id',$request->group_id)->get();

        $res = [
            'data_list'=> $data_list
        ];
        return ApiHelper::JSON_RESPONSE(true,$res,'');
    }
  

    public function create()
    {
        $page_data=WebPages::all();
        $category_data=Category::all();
        $data=[
            'page_data'=>$page_data,
            'category_data'=>$category_data,
        ];

        if($page_data)
            return ApiHelper::JSON_RESPONSE(true,$data,'');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'');

        
    }

    public function store(Request $request)
    {
        $api_token = $request->api_token;


        $validator = Validator::make($request->all(),[
            'group_id' => 'required',
            'menu_name' => 'required',
            'menu_type' => 'required',
            'menu_link' => 'required',

        ],[
            'group_id.required'=>'MENU_GROUP_ID_REQUIRED',
            'menu_name.required'=>'MENU_NAME_REQUIRED',
            'menu_type.required'=>'MENU_TYPE_REQUIRED',
            'menu_link.required'=>'MENU_LINK_REQUIRED',

        ]);
        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());

        $menu_data=$request->only(['group_id','menu_name','menu_type','menu_link']);

        $data = Menu::create($menu_data);

        if($data)
            return ApiHelper::JSON_RESPONSE(true,$data,'SUCCESS_MENU_ADD');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_MENU_ADD');

    }

    public function edit(Request $request)
    {
        // return ApiHelper::JSON_RESPONSE(true,$request->all(),'');
        $api_token = $request->api_token;
        
        $data_list = Menu::where('menu_id',$request->menu_id)->first();
        return ApiHelper::JSON_RESPONSE(true,$data_list,'');

    }

    public function update(Request $request)
    { 

        $api_token = $request->api_token;

        $validator = Validator::make($request->all(),[
            'group_id' => 'required',
            'menu_name' => 'required',
            'menu_type' => 'required',
            'menu_link' => 'required',

        ],[
            'group_id.required'=>'MENU_GROUP_ID_REQUIRED',
            'menu_name.required'=>'MENU_NAME_REQUIRED',
            'menu_type.required'=>'MENU_TYPE_REQUIRED',
            'menu_link.required'=>'MENU_LINK_REQUIRED',

        ]);
        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());

        $menu_update_data=$request->only(['menu_id','group_id','menu_name','menu_type','menu_link']);
        $data = Menu::where('menu_id', $request->menu_id)->update($menu_update_data);


           // return ApiHelper::JSON_RESPONSE(true,$banner_update_data['banners_image'],'');

        if($data)
            return ApiHelper::JSON_RESPONSE(true,$data,'SUCCESS_MENU_UPDATE');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_MENU_UPDATE');
    }


    public function destroy(Request $request)
    {
        $api_token = $request->api_token;

        $status = Menu::where('menu_id',$request->menu_id)->delete();
        if($status) {
            return ApiHelper::JSON_RESPONSE(true,[],'SUCCESS_MENU_DELETE');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_MENU_DELETE');
        }
    }

    public function changeStatus(Request $request)
    {

        $api_token = $request->api_token; 
        $menu_id = $request->menu_id;
        $sub_data = Menu::find($menu_id);
        $sub_data->status = ($sub_data->status == 0 ) ? 1 : 0;         
        $sub_data->save();
        
        return ApiHelper::JSON_RESPONSE(true,$sub_data,'SUCCESS_STATUS_UPDATE');
    }


}
