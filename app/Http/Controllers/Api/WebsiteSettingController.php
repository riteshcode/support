<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use ApiHelper;
use App\Models\WebsiteSettingsGroup;

use App\Models\Currency;
use App\Models\Language;
use App\Models\Country;
use App\Models\TimeZone;

class WebsiteSettingController extends Controller
{


    public function index(Request $request){

        // Validate user page access
        $api_token = $request->api_token;

        $current_page = !empty($request->page)?$request->page:1;
        $perPage = !empty($request->perPage)?$request->perPage:10;
        $search = $request->search;
        $sortBY = $request->sortBy;
        $ASCTYPE = $request->orderBY;

        $data_list = WebsiteSettingsGroup::with(['settings' => function ($query) {
            $query->orderBy('setting_type', 'ASC');
        }])->get();

        //$data_list = WebsiteSetting::all();
        
        // attaching image url of logo etc..
        if(!empty($data_list)){
            foreach ($data_list as $key => $data) {
                $data->settings->map(function($set){
                    $all_image_key = ["website_logo","website_favicon","preloader_image"];
                    $key_image = $set->setting_key.'_image'; 
                    $set->$key_image = in_array($set->setting_key, $all_image_key) 
                                        ? ApiHelper::getFullImageUrl($set->setting_value, '') 
                                        : '';
                    return $set;
                });
            }
        }

        $currency = Currency::select('currencies_code as label','currencies_code as value')->where('status', 1)->get();
        $language = Language::select('languages_code as label','languages_code as value')->where('status', 1)->get();
        $country = Country::select('countries_name as label','countries_iso_code_2 as value')->get();

        $timezone = TimeZone::select('timezone_location as label','timezone_location as value')->get();

        $res = [
            'data_list'=>$data_list,
            'helperData' => [
                'currency'=>$currency,
                'language'=>$language,
                'country'=>$country,
                'timezone'=>$timezone,
            ],
        ];

        return ApiHelper::JSON_RESPONSE(true,$res,'');
    }
    

    public function create()
    {
        $group_data=WebsiteSettingsGroup::all();

    if($group_data)
        return ApiHelper::JSON_RESPONSE(true,$group_data,'');
    else
        return ApiHelper::JSON_RESPONSE(false,[],'');

        
    }


    public function store(Request $request)
    {
        $api_token = $request->api_token;
   

        $validator = Validator::make($request->all(),[
            'group_id' => 'required',
            'setting_key' => 'required',
            'setting_name' => 'required',
            'setting_value' => 'required',
         
        ],[
            'group_id.required'=>'GROUP_ID_REQUIRED',
            'setting_key.required'=>'SETTING_KEY_REQUIRED',
            'setting_name.required'=>'SETTING_NAME_REQUIRED',
            'setting_value.required'=>'SETTING_VALUE_REQUIRED',
                      
        ]);
        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());
    
        $setting_data=$request->except(['api_token']);
        $data = WebsiteSetting::create($setting_data);

        if($data)
            return ApiHelper::JSON_RESPONSE(true,$data,'SUCCESS_WEB_SETTINGS_ADD');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_WEB_SETTINGS_ADD');

    }

    public function edit(Request $request)
    {
        $api_token = $request->api_token;
        
        $data_list = WebsiteSetting::where('setting_id',$request->setting_id)->first();
        return ApiHelper::JSON_RESPONSE(true,$data_list,'');

    }

    public function update(Request $request)
    {

        $all_image_key = ["website_logo","website_favicon","preloader_image"];

        $api_token = $request->api_token;

        $all_data = $request->settingdata;

        if(empty($all_data))
            return ApiHelper::JSON_RESPONSE(false,[],'SUCCESS_WEB_SETTINGS_UPDATE');
        
        foreach($all_data as $key=>$value)
        {
            // return ApiHelper::JSON_RESPONSE(true,$value['setting_key'],'WEBSETTINGS_UPDATED');

            $UPDATE = [];

            if(array_key_exists('setting_key', $value))
                $UPDATE['setting_key'] = $value['setting_key'];    
            
            if(array_key_exists('setting_name', $value))
                $UPDATE['setting_name'] = $value['setting_name'];    
            
            if(array_key_exists('setting_value', $value))
            {
                $UPDATE['setting_value'] = $value['setting_value'];   
                
                 // upload image to live. current in temp
                if(in_array($value['setting_key'], $all_image_key))
                    ApiHelper::image_upload_with_crop($api_token,$value['setting_value'], 6, $value['setting_key']); 
            }
            
            
           


            $setting_data = WebsiteSetting::where('setting_id', $value['setting_id'])->update($UPDATE);
        }

        if($setting_data)
            return ApiHelper::JSON_RESPONSE(true,$setting_data,'SUCCESS_WEB_SETTINGS_UPDATE');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_WEB_SETTINGS_UPDATE');
    }

    public function destroy(Request $request)
    {
        $api_token = $request->api_token;
    
        $status = WebsiteSetting::where('setting_id',$request->setting_id)->delete();
        if($status) {
            return ApiHelper::JSON_RESPONSE(true,[],'SUCCESS_WEB_SETTINGS_DELETE');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_WEB_SETTINGS_DELETE');
        }
    }
}
