<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Modules\Ecommerce\Models\ProductsOptionsValues;

use ApiHelper;

class ProductsOptionsValuesController extends Controller
{

    public $page = 'productsoptionsvalues';
    public $pageview = 'view';
    public $pageadd = 'add';
    public $pagestatus = 'remove';
    public $pageupdate = 'update';


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index(Request $request)
    {
        // Validate user page access
        $api_token = $request->api_token;

        // if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageview))
        //     return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');



        $current_page = !empty($request->page) ? $request->page : 1;
        $perPage = !empty($request->perPage) ? $request->perPage : 10;
        $search = $request->search;
        $sortBY = $request->sortBy;
        $ASCTYPE = $request->orderBY;
        //$language = $request->language;

        $data_query = ProductsOptionsValues::select('products_options_values_id as value_id', 'products_options_id as option_id', 'products_options_values_name as values_name');

        //if(!empty($search))
        // $data_query = $data_query->where("name","LIKE", "%{$search}%")->orWhere("email", "LIKE", "%{$search}%");

        /* order by sorting */
        if (!empty($sortBY) && !empty($ASCTYPE)) {
            $data_query = $data_query->orderBy($sortBY, $ASCTYPE);
        } else {
            $data_query = $data_query->orderBy('products_options_values_id', 'ASC');
        }

        $skip = ($current_page == 1) ? 0 : (int)($current_page - 1) * $perPage;

        $user_count = $data_query->count();

        $data_list = $data_query->get();


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

        // if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageadd))
        //     return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');



        //validation check 
        $rules = [
            'products_options_values_name' => 'required|string',
            'products_options_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiHelper::JSON_RESPONSE(false, [], $validator->messages());
        }

        $products_options_id = $request->products_options_id;
        $products_options_values_name = $request->products_options_values_name;
        $productoptvalarray = explode(",", $products_options_values_name);
        // store Fields 
        //$saveData = $request->only(['sort_order','fieldsgroup_name','fieldsgroup_description']);
        // $saveData['parent_id'] = ($request->category_type == 'parent') ? $request->main_category : $request->sub_category;
        if (!empty($productoptvalarray)) {
            foreach ($productoptvalarray as $value) {
                $prdopval = ProductsOptionsValues::create([
                    'products_options_id' => $products_options_id,
                    'products_options_values_name' => $value,

                ]);
            }
        }

        if ($prdopval) {
            return ApiHelper::JSON_RESPONSE(true, $productoptvalarray, 'PRODUCTOPTIONSVALUES_CREATED');
        } else {
            return ApiHelper::JSON_RESPONSE(false, [], 'SOME_ISSUE');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $response = ProductsOptionsValues::where('products_options_id',$request->products_options_id)->get();
        return ApiHelper::JSON_RESPONSE(true, $response, '');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Validate user page access
        $api_token = $request->api_token;
        

        // if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageadd))
        //     return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');



        //validation check 
        $rules = [
            'products_options_values_name' => 'required|string',
            'products_options_id' => 'required',

        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiHelper::JSON_RESPONSE(false, [], $validator->messages());


        // store fieldsgroup
        //$saveData = $request->only(['sort_order','fieldsgroup_name','status','fieldsgroup_description']); 

        $products_options_id = $request->products_options_id;
        $products_options_values_name = $request->products_options_values_name;
        $productoptvalarray = explode(",", $products_options_values_name);

        ProductsOptionsValues::where('products_options_id',$products_options_id )->delete();;
        //$post->delete();

        // store Fields 
        //$saveData = $request->only(['sort_order','fieldsgroup_name','fieldsgroup_description']);
        // $saveData['parent_id'] = ($request->category_type == 'parent') ? $request->main_category : $request->sub_category;

        foreach ($productoptvalarray as $value) {
            $prdopval = ProductsOptionsValues::create([
                'products_options_id' => $products_options_id,
                'products_options_values_name' => $value,

            ]);
        }

        //$fldg = ProductsOptionsValues::where('products_options_id', $products_options_id)->update($prdopval);


        if ($prdopval) {
            return ApiHelper::JSON_RESPONSE(true, $productoptvalarray, 'FIELDSGROUP_UPDATED');
        } else {
            return ApiHelper::JSON_RESPONSE(false, [], 'SOME_ISSUE');
        }
    }


    // public function changeStatus(Request $request)
    // {
    //     $api_token = $request->api_token;
    //     $infoData = ProductsOptionsValues::where('products_options_id',$request->products_options_id)->get();
    //     $infoData->status = ($infoData->status == 0) ? 1 : 0;
    //     $infoData->save();
    //     return ApiHelper::JSON_RESPONSE(true, $infoData, 'STATUS_UPDATED');
    // }
}
