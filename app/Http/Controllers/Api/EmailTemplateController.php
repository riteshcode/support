<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use ApiHelper;
use App\Models\EmailTemplates;
use App\Models\WebPages;
use Modules\Ecommerce\Models\Category;


class EmailTemplateController extends Controller
{

    public function list(Request $request){


        $api_token = $request->api_token;
        $data_list = EmailTemplates::where('group_id',$request->group_id)->get();

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
            'template_subject' => 'required',
            'template_content' => 'required',

        ],[
            'group_id.required'=>'GROUP_ID_REQUIRED',
            'template_subject.required'=>'SUBJECT_REQUIRED',
            'template_content.required'=>'CONTENT_REQUIRED',

        ]);
        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());

        $menu_data=$request->only(['group_id','template_content','template_subject']);

        $data = EmailTemplates::create($menu_data);

        if($data)
            return ApiHelper::JSON_RESPONSE(true,$data,'SUCCESS_TEMPLATE_ADD');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_TEMPLATE_ADD');

    }

    public function edit(Request $request)
    {
        $api_token = $request->api_token;
        $data_list = EmailTemplates::where('template_id',$request->template_id)->first();
        return ApiHelper::JSON_RESPONSE(true,$data_list,'');

    }

    public function update(Request $request)
    { 

        $api_token = $request->api_token;
        $validator = Validator::make($request->all(),[
            'group_id' => 'required',
            'template_subject' => 'required',
            'template_content' => 'required',

        ],[
            'group_id.required'=>'GROUP_ID_REQUIRED',
            'template_subject.required'=>'SUBJECT_REQUIRED',
            'template_content.required'=>'CONTENT_REQUIRED',

        ]);

        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());

        $menu_update_data=$request->only(['group_id','template_content','template_subject']);
        $data = EmailTemplates::where('template_id', $request->template_id)->update($menu_update_data);


           // return ApiHelper::JSON_RESPONSE(true,$banner_update_data['banners_image'],'');

        if($data)
            return ApiHelper::JSON_RESPONSE(true,$data,'SUCCESS_TEMPLATE_UPDATE');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_TEMPLATE_UPDATE');
    }


    public function destroy(Request $request)
    {
        $api_token = $request->api_token;

        $status = EmailTemplates::where('template_id',$request->template_id)->delete();
        if($status) {
            return ApiHelper::JSON_RESPONSE(true,[],'SUCCESS_TEMPLATE_DELETE');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_TEMPLATE_DELETE');
        }
    }

    public function changeStatus(Request $request)
    {

        $api_token = $request->api_token; 
        $template_id = $request->template_id;
        $sub_data = EmailTemplates::find($template_id);
        $sub_data->status = ($sub_data->status == 0 ) ? 1 : 0;         
        $sub_data->save();
        
        return ApiHelper::JSON_RESPONSE(true,$sub_data,'SUCCESS_STATUS_UPDATE');
    }


}
