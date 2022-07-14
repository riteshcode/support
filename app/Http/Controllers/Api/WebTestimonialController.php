<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\WebTestimonial;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use ApiHelper;



class WebTestimonialController extends Controller
{


    public function index(Request $request)
    {
           // Validate user page access
        $api_token = $request->api_token;
        $current_page = !empty($request->page) ? $request->page : 1;
        $perPage = !empty($request->perPage) ? $request->perPage : 10;
        $search = $request->search;
        $sortBY = $request->sortBy;
        $ASCTYPE = $request->orderBY;
        $language = $request->language;

        $data_query = WebTestimonial::query();

        if (!empty($search))
            $data_query = $data_query->where("testimonial_title","LIKE", "%{$search}%");

            /* order by sorting */
            if (!empty($sortBY) && !empty($ASCTYPE)) {
                $data_query = $data_query->orderBy($sortBY, $ASCTYPE);
            } else {
                $data_query = $data_query->orderBy('testimonial_id', 'ASC');
            }

        $skip = ($current_page == 1) ? 0 : (int)($current_page - 1) * $perPage;

        $user_count = $data_query->count();

        $data_list = $data_query->skip($skip)->take($perPage)->get();


        $res = [
            'data' => $data_list,
            'current_page' => $current_page,
            'total_records' => $user_count,
            'total_page' => ceil((int)$user_count / (int)$perPage),
            'per_page' => $perPage
        ];
        return ApiHelper::JSON_RESPONSE(true, $res, '');

    
  
    }

    
   
   
    

    public function changeStatus(Request $request)
    {

        $api_token = $request->api_token; 
        $testimonial_id = $request->testimonial_id;
        $infodata=WebTestimonial::find($testimonial_id);
        $infodata->status = ($infodata->status == 0 ) ? 1 : 0;         
        $infodata->save();
      
        return ApiHelper::JSON_RESPONSE(true,$infodata,'SUCCESS_STATUS_UPDATE');
    }


}
