<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Subscriber;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanToIndustry;
use App\Models\SubscriptionHistory;
use App\Models\SubscriptionTransaction;
use App\Models\Industry;
use Illuminate\Http\Request;
use ApiHelper;
use App\Mail\StatusChangeMail;
use Illuminate\Support\Facades\Mail;
use App\Jobs\StatusUpdateMail;

class SubscriptionController extends Controller
{
    public $page = 'user';
    public $pageview = 'view';
    public $pageadd = 'add';
    public $pagestatus = 'remove';
    public $pageupdate = 'update'; 


    //This Function is used to show the list of subscriptions
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
        $ASCTYPE = $request->orderBy;

        /*Fetching Subscription data*/ 
        $subscription_query = Subscription::query();
        /*Checking if search data is not empty*/
        if(!empty($search))
            $subscription_query = $subscription_query
        ->where("subscription_unique_id","LIKE", "%{$search}%");

        /* order by sorting */
        if(!empty($sortBy) && !empty($ASCTYPE))
            $subscription_query = $subscription_query->orderBy($sortBy,$ASCTYPE);
        else
            $subscription_query = $subscription_query->orderBy('subscription_id','DESC');

        $skip = ($current_page == 1)?0:(int)($current_page-1)*$perPage;

        $subscription_count = $subscription_query->count();

        $subscription_list = $subscription_query->skip($skip)->take($perPage)->get();
        /*Checking if subscription list is not empty*/
        if (!empty($subscription_list)) { 
            
            $subscription_list->map(function($data){
                /* Checking if subscriber name is not empty*/
                $sub_status = ['InActive','Active','Limited','Blocked'];
                $data->status = $sub_status[(int)$data->status];
                if(!empty($data->industry_details)){
                    $data->subscriber_industry = $data->industry_details->industry_name;
                }
                if(!empty($data->subscriber_business))
                    $data->subscriber_business_name = $data->subscriber_business->business_name;
                

                $transaction_details = $data->subscription_transaction()->where('payment_status', '2')->first(); 
                if($transaction_details == null){
                    $transaction_details = $data->subscription_transaction()->where('payment_status', '1')->first();
                    if($transaction_details == null) {
                        $transaction_details = $data->subscription_transaction()->where('payment_status', '0')->first();
                        if($transaction_details == null){
                            $transaction_details = $data->subscription_transaction()->where('payment_status', '3')->first();
                        }
                    }
                }

                $payment_st = ['Pending','Paid','Success','Failed'];
                $approve_st = ['Pending','Approved','Reject'];

                if($transaction_details){
                    $data->payment_date = $transaction_details->created_at;
                    $data->subscriber_approval_status = $approve_st[(int)$transaction_details->approval_status];
                    $data->approval_date = $transaction_details->approved_at;
                    $data->payment_status = $payment_st[(int)$transaction_details->payment_status];

                    if($transaction_details->subscription_history_details){
                        $data->subscriber_plan_name = $transaction_details->subscription_history_details->plan_name;
                        $data->subscriber_plan_duration = $transaction_details->subscription_history_details->plan_duration;
                    }
                }
                
                /* Checking subscription status*/
                return $data;
            });
        }
        /*Binding data into $res variable*/
        $res = [
            'data'=>$subscription_list,
            'current_page'=>$current_page,
            'total_records'=>$subscription_count,
            'total_page'=>ceil((int)$subscription_count/(int)$perPage),
            'per_page'=>$perPage,
        ];

        return ApiHelper::JSON_RESPONSE(true,$res,'');
    }
    
    //This Function is used to show the particular subscription data
    public function edit(Request $request){

        $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageupdate)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }
        $subscription_id = $request->subscription_id;
        $data=Subscription::where('subscription_id',$subscription_id)->first();
       // $data = Subscription::with('subscription_history')->where('subscription_id', $subscription_id)->first();
          return ApiHelper::JSON_RESPONSE(true,$data,'');
     
    }
    
    //This Function is used to update the particular subscription data
    public function update(Request $request){

        $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageupdate)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }

        /*fetching data from api*/
        $subscription_id = $request->subscription_id;
        $expired_at = $request->expired_at;
        $status = $request->status;
        /*validating data*/
        $validator = Validator::make($request->all(),[
            'expired_at' => 'required',
        ],[
            'expired_at.required' => 'EXPIRED_AT_REQUIRED',
        ]);
        /*if validation fails then it will show error message*/
        if ($validator->fails()) {
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());
        }
        /*updating subscription data after validation*/  
        $data = Subscription::where('subscription_id', $subscription_id)->update(['expired_at'=>$expired_at, 'status'=>$status]);
        
        // you are returning somethign to client side.
        return ApiHelper::JSON_RESPONSE(true,$data,'SUCCESS_SUBSCRIPTION_UPDATE');
    }
    
    //This Function is used to get the details of subscription data
     public function details(Request $request){
        $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageview)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }

        $subscription_id = $request->subscription_id;
        $data = Subscription::with('subscriber_business','subscriber_business.business_info','industry_details','subscription_history','subscriber_business.business_info.country','subscription_transaction')->where('subscription_id', $subscription_id)->first();

       // $data->subscriber_business = $data->subscriber_business;
        //$data->subscriber_business_info = $data->subscriber_business->business_info;
        if(!empty($data)){
            $data->billing_country = $data->countries_name;
        }
        $data->subscriber_industry = $data->industry_name;
        if($data->subscription_history){
            $data->subscription_history = $data->subscription_history->map(function($history){
                if($history->approval_status == 0){
                    $history->approval_status = 'Pending';
                }else{
                    $history->approval_status = 'Approved';
                }
                $history->subscription_transaction = $history->subscription_transaction->map(function($transaction){
                    if(!empty($transaction->payment_method_details)){
                        $transaction->payment_method_name = $transaction->payment_method_details->method_key;
                    }
                    $payment_st = ['Pending','Paid','Success','Failed'];
                    $approve_st = ['Pending','Approved','Reject'];
                    $transaction->payment_status = $payment_st[(int)$transaction->payment_status];
                    $transaction->approval_status = $approve_st[(int)$transaction->approval_status];
                    return $transaction;
                });

                return $history;
            });
        }else{
            $data->subscription_history = $data->subscription_history;
        }

        
        return ApiHelper::JSON_RESPONSE(true,$data,'');
    }
    
    //This Function is used to get the change the subscription status
    public function changeStatus(Request $request)
    {

         $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageupdate)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }
        $subscription_id = $request->subscription_id;
        $status = $request->status;
        $sub_data = Subscription::where('subscription_id', $subscription_id)->first();
           
            if($sub_data->status =='0'){
                $data = Subscription::where('subscription_id', $subscription_id)->update(['status'=> '0']);
                $status = 'InActive';
            }elseif($sub_data->status =='1'){
                $data = Subscription::where('subscription_id', $subscription_id)->update(['status'=> '1']);
                $status = 'Active';
            }elseif($sub_data->status =='2'){
                $data = Subscription::where('subscription_id', $subscription_id)->update(['status'=> '2']);
                $status = 'Limited';
            }{
                $data = Subscription::where('subscription_id', $subscription_id)->update(['status'=> '3']);
                $status = 'Blocked';
            }
        return ApiHelper::JSON_RESPONSE(true,$data,'SUCCESS_STATUS_UPDATE');
    }

    public function industry_plan(Request $request)
    {
        $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageview)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }
        $industry_id = $request->industry_id;
        $industry_data = SubscriptionPlanToIndustry::where('industry_id',$industry_id)->get();
        return ApiHelper::JSON_RESPONSE(true,$industry_data,'SUCCESS_PLAN_DATA_UPDATE');
        // $industry_data = $industry_data->map(function($data){
        //     if(!empty($data->subscription_plan_details)){
        //         $data->subscription_plan_details = $data->subscription_plan_details;
        //     }
        //     return $data;
        // }); 
        // return ApiHelper::JSON_RESPONSE(true,$industry_data,'SUCCESS_PLAN_DATA_UPDATE');
    }

    public function add(Request $request)
    {
        $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageadd))
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');

        $business_id = $request->business_id;
        $plan_id = $request->plan_id;
        $industry_id = $request->industry_id;
        
        $validator = Validator::make($request->all(),[
            'business_id' => 'required',
            'industry_id' => 'required',
            'plan_id' => 'required',
        ],[
            'business_id.required' => 'BUSINESS_ID_REQUIRED',
            'industry_id.required' => 'INDUSTRY_ID_REQUIRED',
            'plan_id.required' => 'PLAN_ID_REQUIRED',
        ]);

        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());

        $subscription_data = Subscription::where('business_id',$business_id)->where('industry_id',$industry_id)->first();
        
        if($subscription_data == null ){
            $subscription_data = Subscription::create([
                'subscription_unique_id'=>ApiHelper::generate_random_token('alpha_numeric',10),
                'business_id'=>$business_id,
                'industry_id'=>$industry_id,
            ]);
        }

        $plan_data = SubscriptionPlan::where('plan_id',$plan_id)->first();
        
        $subscription_history_create = SubscriptionHistory::create([
            'subscription_id' => $subscription_data->subscription_id,
            'plan_id' => $plan_id,
            'plan_name' => $plan_data->plan_name,
            'plan_amount' => $plan_data->plan_amount,
            'plan_discount' => $plan_data->plan_discount,
            'plan_duration' => $plan_data->plan_duration,
        ]);

        if($subscription_history_create){
            return ApiHelper::JSON_RESPONSE(true, $subscription_data,'SUCCESS_SUBSCRIPTION_ADD');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_SUBSCRIPTION_ADD');
        }  
    }

    public function history($id)
    {
       $subscription_history = SubscriptionHistory::where('subs_history_id',$id)->first();
       if(!empty($subscription_history->subscription_transaction)){
            if($subscription_history->subscription_transaction->payment_status == 0){
                $subscription_history->payment_status = 'Pending';
            }elseif($subscription_history->subscription_transaction->payment_status == 1){
                $subscription_history->payment_status = 'Paid';
            }elseif($subscription_history->subscription_transaction->payment_status == 2){
                $subscription_history->payment_status = 'Success';
            }else{
                $subscription_history->payment_status = 'Failed';
            }
        }
        return ApiHelper::JSON_RESPONSE(true,$subscription_history,''); 
    }
}
