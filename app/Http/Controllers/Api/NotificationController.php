<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use ApiHelper;

use App\Events\MyEvent;


class NotificationController extends Controller
{

    public function get_all_notification(Request $request){
        
        
    
        $api_token = $request->api_token;
        $user_id = ApiHelper::get_adminid_from_token($api_token);
        $list = Notification::where('user_id',$user_id)->get();
        if(!empty($list)){
            $list->map(function($data){
                $data->message  =  $data->getNotiMessage;
                $data->userInfo = $data->userInfo;
                return $data;
            });
        }

        return ApiHelper::JSON_RESPONSE(true,$list,'');
    }

    public function get_unread_notification(Request $request){
        
        $api_token = $request->api_token;
        $user_id = ApiHelper::get_user_id_from_token($api_token);
        $list = Notification::where('user_id',$user_id)->where('is_read',0)->get();

        if(!empty($list)){
            $list->map(function($data){
                $data->message  =  $data->getNotiMessage;
                $data->userInfo = $data->userInfo;
                return $data;
            });
        }
        return ApiHelper::JSON_RESPONSE(true,$list,'');
    }

    public function is_read_status(Request $request){
        $api_token = $request->api_token;
        $read_id = $request->notification_user_id;
        $data = Notification::where('notification_user_id',$read_id)->update([
           'is_read'=>1,
           'read_at'=>date("Y-m-d h:s:i")
        ]);
        return ApiHelper::JSON_RESPONSE(true,$data,'');
    }


    


}
