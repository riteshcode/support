<?php

namespace Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ApiHelper;
use Modules\HRM\Models\Attendance;
use Illuminate\Support\Str;
use Validator;


class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        // Validate user page access
        $api_token = $request->api_token;

        $user_id = $request->user_id;
        $month = $request->month;
        $year = date('Y');

        $data_query = Attendance::query();

        if(!empty($user_id) && !empty($month))
            $data_query = $data_query->where('user_id', $user_id)->where('month',$month)->where('year', $year);
        else
            $data_query = $data_query->where('day', date('d'))->where('month',date('m'))->where('year', $year);

        $data_list = $data_query->get();

        if (!empty($data_list)) { 
            $data_list->map(function($data){
                $data->user_name = $data->user->first_name.' '.$data->user->last_name;
                $data->attendance_date = $data->day.'-'.$data->month.'-'.$data->year;
                return $data;
            });
        }

        return ApiHelper::JSON_RESPONSE(true,$data_list,'');
    }

    public function checkIn(Request $request){
        
        $api_token = $request->api_token;
        $inp_data = [
            'user_id' => ApiHelper::get_user_id_from_token($api_token),
            'day' => date('d'),
            'month' => date('m'),
            'year' => date('Y'),
        ];

        $check = Attendance::where($inp_data)->first();
        if($check == null){

            $inp_data['check_in'] = date('h:i:s A');
            $inp_data['attendance_date'] = date('Y-m-d h:i:s');
            $res = Attendance::create($inp_data);

            return ApiHelper::JSON_RESPONSE(true,$res,'');
        
        }else
            return ApiHelper::JSON_RESPONSE(false,[],'TODAY_ATTENDANCE_FOUND');
    }

    public function checkOut(Request $request){
        
        $api_token = $request->api_token;
        $inp_data = [
            'user_id' => ApiHelper::get_user_id_from_token($api_token),
            'day' => date('d'),
            'month' => date('m'),
            'year' => date('Y'),
        ];

        $data = Attendance::where($inp_data)->first();
        if($data !== null){
            $res = Attendance::where('attendance_id', $data->attendance_id)->update(['check_out' => date('h:i:s A')]);
            return ApiHelper::JSON_RESPONSE(true,$res,'');
        }else
            return ApiHelper::JSON_RESPONSE(false,[],'TODAY_ATTENDANCE_NOT_FOUND');
    

    }

}
