<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Language;
use ApiHelper;
use Illuminate\Support\Facades\Storage;
use App\Models\TemplateComponent;
use App\Models\SubsComponentSetting;
use App\Models\ComponentSetting;
use App\Models\Templates;




class TemplateComponentSettingController extends Controller
{




    public function index(Request $request)
    {
        // Validate user page access
        $api_token = $request->api_token;
        $template_id = $request->template_id;
        $data_list = TemplateComponent::where('parent_id',0)->get();
        if(!empty($data_list)){
        
        $data_list->map(function($data) use($template_id){

            $child_key = TemplateComponent::where('parent_id', $data->component_id)->get();

            if(!empty($child_key)){
                $child_key->map(function($child) use($template_id){
                    
                    $res = ComponentSetting::where([
                        'template_id'=>$template_id,
                        'component_id'=> $child->component_id,
                    ])->first();

                    if(!empty($res)){
                    
                        $resS = SubsComponentSetting::where('setting_id', $res->setting_id)->first();

                        $child->statusCheck = !empty($resS) ? $resS->status : $child->status;
                        $child->sortOrder = !empty($resS) ? $resS->sort_order : $child->sort_order;

                    
                    }

                    $child->setting_id = !empty($res) ? $res->setting_id : '';
                  //  $child->ComponentSetting = $res;

                    return $child;
                });

            }
            $data->child = $child_key;

            return $data;
        });

        }
        

        $res = [
            'child_list' => $data_list,
            'template_detail'=>Templates::find($request->template_id),
        ];
        return ApiHelper::JSON_RESPONSE(true, $res, '');
    }

    
    public function create()
    {
        //
    }

   
    public function show($id)
    {
        //
    }


    public function sortOrder(Request $request)
    {
        $api_token = $request->api_token;
        $setting_id=$request->setting_id;
        $sort_order=$request->sort_order;
        $infoData = SubsComponentSetting::find($setting_id);
        if(empty($infoData)){
            $infoData = new SubsComponentSetting();
            $infoData->setting_id=$setting_id;
            $infoData->sort_order =$sort_order;
            $infoData->status =1;

            $infoData->save();
        
        }else{
            $infoData->sort_order = $sort_order;
            $infoData->save();
        }
       
        return ApiHelper::JSON_RESPONSE(true, $infoData, 'SUCCESS_SORT_ORDER_UPDATE');
    }    

   

    public function changeStatus(Request $request)
    {
        $api_token = $request->api_token;
        $setting_id=$request->setting_id;
        $infoData = SubsComponentSetting::find($setting_id);
        if(empty($infoData)){
            $infoData = new SubsComponentSetting();
            $infoData->setting_id=$setting_id;
            $infoData->status = ($infoData->status == 0) ? 1 : 0;
            $infoData->save();
        
        }else{
            $infoData->status = ($infoData->status == 0) ? 1 : 0;
            $infoData->save();
        }

        return ApiHelper::JSON_RESPONSE(true, $infoData, 'SUCCESS_STATUS_UPDATE');
    }
}
