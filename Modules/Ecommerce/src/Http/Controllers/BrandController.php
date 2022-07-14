<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ApiHelper;
use Illuminate\Support\Str;
use Validator;
use Auth;
use Modules\Ecommerce\Models\Brand;
class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $BrandView = 'brand-view';
    public $BrandManage = 'brand-manage';
    public $BrandDelete = 'brand-delete';

    public function index(Request $request){

        // Validate user page access
        $api_token = $request->api_token;

       /* if(!ApiHelper::is_page_access($api_token, $this->BrandView)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }
        */
        $current_page = !empty($request->page)?$request->page:1;
        $perPage = !empty($request->perPage)?$request->perPage:10;
        $search = $request->search;
        $sortBY = $request->sortBy;
        $ASCTYPE = $request->orderBY;

        $data_query = Brand::query();

        // search
        if(!empty($search))
            $data_query = $data_query->where("brand","LIKE", "%{$search}%");

        /* order by sorting */
        if(!empty($sortBY) && !empty($ASCTYPE)){
            $data_query = $data_query->orderBy($sortBY,$ASCTYPE);
        }else{
            $data_query = $data_query->orderBy('brand_id','ASC');
        }

        $skip = ($current_page == 1)?0:(int)($current_page-1)*$perPage;     // apply page logic

        $data_count = $data_query->count(); // get total count

        $data_list = $data_query->skip($skip)->take($perPage)->get(); 
        
       

        $res = [
            'data'=>$data_list,
            'current_page'=>$current_page,
            'total_records'=>$data_count,
            'total_page'=>ceil((int)$data_count/(int)$perPage),
            'per_page'=>$perPage
        ];

        return ApiHelper::JSON_RESPONSE(true,$res,'');
    }

    public function store(Request $request)
    {
        $api_token = $request->api_token;

       /* if(!ApiHelper::is_page_access($api_token,$this->BrandManage))
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        */
        $validator = Validator::make($request->all(),[
            'brand' => 'required',
        ],[
            'brand.required'=>'BRAND_REQUIRED',
            // 'brand.unique'=>'brand_UNIQUE',
        ]);
        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());
        

        $user_id = ApiHelper::get_adminid_from_token($api_token);

        $Insert = $request->only('brand');
        $Insert['created_by'] = $user_id;

        $res = Brand::create($Insert);
        if($res)
            return ApiHelper::JSON_RESPONSE(true,$res->id,'BRAND_CREATED');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'UNABLE_BRAND_CREATED');

    }

    public function edit(Request $request)
    {
        $api_token = $request->api_token;
        /*
        if(!ApiHelper::is_page_access($api_token,$this->BrandManage)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }
        */
        $data_list = Brand::where('brand_id',$request->brand_id)->first();
        return ApiHelper::JSON_RESPONSE(true,$data_list,'');

    }

    public function update(Request $request)
    {
        $api_token = $request->api_token;
        $brand_id = $request->brand_id;
        /*
        if(!ApiHelper::is_page_access($api_token,$this->BrandManage)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }
        */

        $user_id = Auth::id();

        $validator = Validator::make($request->all(),[
            'brand' => 'required|unique:sa_brands',
        ],[
            'brand.required'=>'BRAND_REQUIRED',
            'brand.unique'=>'BRAND_UNIQUE',
        ]);
        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());
        

        $user_id = ApiHelper::get_adminid_from_token($api_token);

        $Insert = $request->only('brand');

        $res = Brand::where('brand_id',$brand_id)->update($Insert);

        if($res)
            return ApiHelper::JSON_RESPONSE(true,$res,'BRAND_UPDATED');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'UNABLE_BRAND_UPDATED');
    }

    public function destroy(Request $request)
    {
        $api_token = $request->api_token;
        $brand_id = $request->brand_id;
        /*
        if(!ApiHelper::is_page_access($api_token,$this->BrandDelete)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }
        */
        $status = Brand::where('brand_id',$brand_id)->delete();
        if($status) {
            return ApiHelper::JSON_RESPONSE(true,[],'BRAND_DELETED');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'NOT_BRAND_DELETED');
        }
    }

}
