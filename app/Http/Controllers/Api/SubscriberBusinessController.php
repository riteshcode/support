<?php


namespace App\Http\Controllers\Api;

use App\Models\SubscriberBusiness;
use App\Models\BusinessInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use ApiHelper;


class SubscriberBusinessController extends Controller
{
    public $page = 'user';
    public $pageview = 'view';
    public $pageadd = 'add';
    public $pagestatus = 'remove';
    public $pageupdate = 'update'; 

    public function index_all(Request $request){
        $api_token = $request->api_token;
        $subscriber_business_list = SubscriberBusiness::where('status',1)->orWhere('status',2)->get();
        return ApiHelper::JSON_RESPONSE(true,$subscriber_business_list,'');
    } 

    //This Function is used to show the list of subscribers
    public function index(Request $request)
    {
        // Validate user page access
        $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageview)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }

        $current_page = !empty($request->page)?$request->page:1;
        $perPage = !empty($request->perPage)?$request->perPage:10;
        $search = $request->search;
        $sortBy = $request->sortBy;
        $orderBy = $request->orderBy;
        
        /*Fetching subscriber data*/ 
        $subscriber_query = SubscriberBusiness::query();
        /*Checking if search data is not empty*/
        if(!empty($search))
            $subscriber_query = $subscriber_query
                ->where("business_unique_id","LIKE", "%{$search}%")
                ->orWhere("business_name","LIKE", "%{$search}%")
                ->orWhere("business_email", "LIKE", "%{$search}%");

        /* order by sorting */
        if(!empty($sortBy) && !empty($orderBy))
            $subscriber_query = $subscriber_query->orderBy($sortBy,$orderBy);
        else
            $subscriber_query = $subscriber_query->orderBy('business_unique_id','DESC');

        $skip = ($current_page == 1)?0:(int)($current_page-1)*$perPage;

        $subscriber_count = $subscriber_query->count();

        $subscriber_list = $subscriber_query->skip($skip)->take($perPage)->get();

        $subscriber_list = $subscriber_list->map(function($data){
            if($data->status=='0'){
                $data->status = 'Deactive'; 
            }else{
                $data->status = 'Active';
            }
            if(!empty($data->business_info->country)){
               $data->business_country = $data->business_info->country->countries_name;
            }
            if(!empty($data->subscription)){
               $data->total_subscription = $data->subscription()->count();
            }
            return $data;
        }); 
        
         /*Binding data into a variable*/
        $res = [
            'data'=>$subscriber_list,
            'current_page'=>$current_page,
            'total_records'=>$subscriber_count,
            'total_page'=>ceil((int)$subscriber_count/(int)$perPage),
            'per_page'=>$perPage,
        ];
        return ApiHelper::JSON_RESPONSE(true,$res,'');
    }
    
    //This Function is used to get the details of subscriber data
    public function changeStatus(Request $request){

        // Validate user page access
        $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageupdate)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }
        $business_id = $request->business_id;
        $status = $request->status;
        $sub_data = SubscriberBusiness::where('business_id', $business_id)->first();
           
            if($sub_data->status =='0'){
                $data = SubscriberBusiness::where('business_id', $business_id)->update(['status'=> '0']);
                $status = 'Pending Approval';
            }elseif($sub_data->status =='1'){
                $data = SubscriberBusiness::where('business_id', $business_id)->update(['status'=> '1']);
                $status = 'Active';
            }elseif($sub_data->status =='2'){
                $data = SubscriberBusiness::where('business_id', $business_id)->update(['status'=> '2']);
                $status = 'Limited';
            }{
                $data = SubscriberBusiness::where('business_id', $business_id)->update(['status'=> '3']);
                $status = 'Blocked';
            }
            
            return ApiHelper::JSON_RESPONSE(true,$data,'SUCCESS_STATUS_UPDATE');
    }

    //This Function is used to show the particular subscriber data
    public function edit(Request $request){

        // Validate user page access
        $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageupdate)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }
        $business_id = $request->business_id;
        $data = SubscriberBusiness::where('business_id', $business_id)->first();
        
        $data->business_info = $data->business_info;
        return ApiHelper::JSON_RESPONSE(true,$data,'');      
    }
    
    //This Function is used to update the particular subscriber data
    public function update(Request $request){

        // Validate user page access
        $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageupdate)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }
        $business_id = $request->business_id;
        $billing_create = $request->only(['billing_city', 'billing_contact_name', 'billing_country', 'billing_default', 'billing_email', 'billing_gst', 'billing_phone', 'billing_state', 'billing_street_address', 'billing_zipcode']);
        $business_name = $request->business_name;
        $business_email = $request->business_email;
        $validator = Validator::make($request->all(),[
            'business_name' => 'required',
            'business_email' => 'required',
        ],[
            'business_name.required'=>'BUSINESS_NAME_REQUIRED',
            'business_email.required'=>'BUSINESS_EMAIL_REQUIRED',
        ]);
        
        if ($validator->fails()){
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());
        }
        $data = SubscriberBusiness::where('business_id',$business_id)->update([
            'business_name'=>$business_name,
            'business_email'=>$business_email,
        ]);
        $billing_data = BusinessInfo::where('business_id',$business_id)->update($billing_create);
        if($data){
            return ApiHelper::JSON_RESPONSE(true,$data,'SUCCESS_SUBSCRIBER_BUSINESS_UPDATE');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_SUBSCRIBER_BUSINESS_UPDATE');
        }
    }
    
    //This Function is used to add the subscriber data
    public function add(Request $request){

        // Validate user page access
        $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageadd)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }
        
        $billing_create = $request->only(['billing_city', 'billing_contact_name', 'billing_country', 'billing_default', 'billing_email', 'billing_gst', 'billing_phone', 'billing_state', 'billing_street_address', 'billing_zipcode']);
        $business_unique_id = $request->business_unique_id; 
        $business_name = $request->business_name;
        $business_email = $request->business_email;
        $validator = Validator::make($request->all(),[
            'business_name' => 'required',
            'business_email' => 'required',
        ],[
            'business_name.required'=>'BUSINESS_NAME_REQUIRED',
            'business_email.required'=>'BUSINESS_EMAIL_REQUIRED',
        ]);
        
        if ($validator->fails()){
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());
        }
        $data = SubscriberBusiness::insertGetId([
            'business_unique_id'=>ApiHelper::generate_random_token('alpha_numeric',15),
            'business_name'=>$business_name,
            'business_email'=>$business_email,
        ]);
        $billing_create['business_id'] = $data;
        $billing_data = BusinessInfo::create($billing_create);
        if($data){
            return ApiHelper::JSON_RESPONSE(true,[],'SUCCESS_SUBSCRIBER_BUSINESS_ADD');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_SUBSCRIBER_BUSINESS_ADD');
        }
    }

}
