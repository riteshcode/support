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
use App\Models\ComponentSetting;




class TemplateComponentController extends Controller
{

    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */



    public function index(Request $request)
    {
        // Validate user page access
        $api_token = $request->api_token;
        $data_list = TemplateComponent::all();
        
        if(!empty($data_list)){
         
         $data_list->map(function($data){
          
          $data->parentName=TemplateComponent::find($data->parent_id);

           $data->parent_id = !empty($data) ? $data->component_name : '';
          
        //  $data->parent_id=$data->component_name;

          return $data;



         });



        }
        

        $res = [
            'data' => $data_list,
        ];
        return ApiHelper::JSON_RESPONSE(true, $res, '');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
       $api_token = $request->api_token;
       $data_list = TemplateComponent::where('parent_id',0)->get();

        return ApiHelper::JSON_RESPONSE(true, $data_list, '');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate user page access
        $api_token = $request->api_token;
        $rules = [
            'sort_order' => 'required',
            'component_name'=>'required',
            'component_key'=>'required',
            'parent_id'=>'required',

        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiHelper::JSON_RESPONSE(false, [], $validator->messages());
        }

        $saveData = $request->only(['sort_order','component_name','component_key','parent_id']);

        $component = TemplateComponent::create($saveData);

        if ($component) {
            return ApiHelper::JSON_RESPONSE(true, [], 'SUCCESS_COMPONET_ADD');
        } else {
            return ApiHelper::JSON_RESPONSE(false, [], 'ERROR_COMPONET_ADD');
        }
    }


    public function show($id)
    {
        //
    }

    
    public function edit(Request $request)
    {
        $response = TemplateComponent::find($request->component_id);

        return ApiHelper::JSON_RESPONSE(true, $response, '');
    }

   
    public function update(Request $request)
    {
        // Validate user page access
        $api_token = $request->api_token;
        $component_id = $request->component_id;

        // store pages
        $saveData = $request->only(['sort_order','component_name','component_key','parent_id']);
        $component = TemplateComponent::where('component_id', $component_id)->update($saveData);
        if ($component) {
            return ApiHelper::JSON_RESPONSE(true, $saveData, 'SUCCESS_COMPONENT_UPDATE');
        } else {
            return ApiHelper::JSON_RESPONSE(false, [], 'ERROR_COMPONENT_UPDATE');
        }
    }

   

    public function changeStatus(Request $request)
    {
        $api_token = $request->api_token;
        $infoData = TemplateComponent::find($request->component_id);
        $infoData->status = ($infoData->status == 0) ? 1 : 0;
        $infoData->save();
        return ApiHelper::JSON_RESPONSE(true, $infoData, 'SUCCESS_STATUS_UPDATE');
    }
}
