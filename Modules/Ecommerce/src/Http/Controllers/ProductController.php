<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ApiHelper;
use Validator;
use Illuminate\Support\Str;
use App\Models\Language;
use Modules\Ecommerce\Models\Category;
use Modules\Ecommerce\Models\CategoryDescription;
use Modules\Ecommerce\Models\Fields;
use Modules\Ecommerce\Models\Product;
use Modules\Ecommerce\Models\ProductType;
use Modules\Ecommerce\Models\ProductOptions;
use Modules\Ecommerce\Models\ProductsOptionsValues;
use Modules\Ecommerce\Models\ProductAttribute;
use Modules\Ecommerce\Models\ProductDescription;
use Modules\Ecommerce\Models\ProductsTypeFieldValue;
use Illuminate\Support\Facades\Storage;
use Modules\Ecommerce\Models\Supplier;
use Modules\Ecommerce\Models\Brand;
use Modules\Ecommerce\Models\SeoMeta;




class ProductController extends Controller
{
    
    public $page = 'products';
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

        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageview))
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        
        $current_page = !empty($request->page)?$request->page:1;
        $perPage = !empty($request->perPage)?$request->perPage:10;
        $search = $request->search;
        $sortBY = $request->sortBy;
        $ASCTYPE = $request->orderBY;
        $language = $request->language;
        
        $data_query = Product::query();
        
        if(!empty($search))
            // $data_query = $data_query->where("name","LIKE", "%{$search}%")->orWhere("email", "LIKE", "%{$search}%");

            /* order by sorting */
        if(!empty($sortBY) && !empty($ASCTYPE)){
            $data_query = $data_query->orderBy($sortBY,$ASCTYPE);
        }else{
            $data_query = $data_query->orderBy('product_id','DESC');
        }

        $skip = ($current_page == 1)?0:(int)($current_page-1)*$perPage;
        
        $user_count = $data_query->count();

        $data_list = $data_query->skip($skip)->take($perPage)->get();

        $data_list = $data_list->map(function($data) use ($language)  {

            $cate = $data->productdescription()->where('languages_id', ApiHelper::getLangid($language))->first();

            $data->products_name = ($cate == null) ? '' : $cate->products_name;
            $data->products_description = ($cate == null) ? '' : $cate->products_description;
            $data->status = ($data->status == 1) ? "active":"deactive"; 

            

            $data->product_image = ApiHelper::getFullImageUrl($data->product_image);

            return $data;
        });
        
        $res = [
            'data'=>$data_list,
            'current_page'=>$current_page,
            'total_records'=>$user_count,
            'total_page'=>ceil((int)$user_count/(int)$perPage),
            'per_page'=>$perPage
        ];
        return ApiHelper::JSON_RESPONSE(true,$res,'');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * use this function to get all helper data for product create. 
     */
    public function create(Request $request)
    {
        $language = $request->language;

        $res = [];

        $categoryItem = array();

        $cat = Category::where('parent_id',0)->get();

        foreach ($cat as $key => $cat) {
            $cate = $cat->categorydescription()->where('languages_id', ApiHelper::getLangid($language))->first();
            array_push($categoryItem, 
                [
                    "value"=>$cat->categories_id, 
                    "label"=>($cate == null) ? '' : $cate->categories_name
                ]);
            // sub category import

            $sub_category = Category::where('parent_id',$cat->categories_id)->get();
            if(sizeof($sub_category) >  0){
               
               foreach ($sub_category as $key => $sub) {
                   $subcat = $sub->categorydescription()->where('languages_id', ApiHelper::getLangid($language))->first();
                    
                    array_push($categoryItem, [ 
                        "value"=>$sub->categories_id, 
                        "label"=>($subcat == null) ? '' : '--'.$subcat->categories_name
                    ]);

               }

            }



        }

        $res['category'] = $categoryItem;
        
        $res['product_type'] = ProductType::all()->map(function($product_type){
            if(!empty($product_type->fields_group)){
                $product_type->fields_group = $product_type->fields_group->map(function($fields_group){
                    $fields_group->fields = $fields_group->fields;
                    return $fields_group;
                });
            }
            return $product_type;
        });
        $res['language'] = Language::all();
        $res['product_attribute'] = ProductOptions::product_options_with_value();
        $res['supplier'] = Supplier::select('supplier_id as value','supplier_name as label')->get();
        $res['brand'] = Brand::all();
        
        return ApiHelper::JSON_RESPONSE(true,$res,'');
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
        

        // return ApiHelper::JSON_RESPONSE(false,$res,'PAGE_ACCESS_DENIED');
        


        // validation check 
        // $rules = [
        //     'categories_name.*' => 'required|string',
        //     'sort_order' => 'required',
        // ];
        // $validator = Validator::make($request->all(), $rules);
        // if ($validator->fails()) {
        //     return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());
        // }



        // store category 
        $saveData = $request->only(['product_model','product_condition','product_sku','product_video_url','sort_order','product_status','product_condition','product_type_id','product_stock_qty','product_price_type','product_stock_price','product_external_url','product_profit_margin','product_sale_price','product_discount_type','product_discount_amount','product_brand_id']);

        $saveData['product_slug'] = "";

        
        

        $product = Product::create($saveData);

        // image and gallery store
        
        if($request->has("product_image"))
            ApiHelper::image_upload_with_crop($api_token,$request->product_image, 1, $product->product_id);

        if($request->has('gallery_ids')){

            $insData = [];

            if (sizeof($request->gallery_ids)) {
                foreach ($request->gallery_ids as $key => $gallery) {
                    ApiHelper::image_upload_with_crop($api_token,$gallery, 1, $product->product_id,'gallery');
                    array_push($insData,[
                        'product_id'=>$product->product_id,
                        'images_id'=>$gallery
                    ]);
                }
                $product->products_to_gallery()->attach($insData);
            }
        }
        

        // store cat details
        foreach (Language::all() as $key => $value) {

            $name = "products_name_".$value->languages_id;
            $desc = "products_description_".$value->languages_id;
            $meta_title = "seometa_title_".$value->languages_id;
            $meta_desc = "seometa_desc_".$value->languages_id;
            
            if($value->languages_code == 'en'){
                $Product = Product::find($product->product_id);
                $Product->product_slug = Str::slug($request->$name);
                $Product->save();
            }

            ProductDescription::create([
                'products_id'=>$product->product_id,
                'languages_id'=>$value->languages_id,
                'products_name'=>$request->$name,
                'products_description'=>$request->$desc,
            ]);   
            
            if(!empty($request->$meta_title) || !empty($request->$meta_desc)){
                SeoMeta::create([
                    'page_type'=>'product',
                    'reference_id'=>$product->product_id,
                    'language_id'=>$value->languages_id,
                    'seometa_title'=>$request->$meta_title,
                    'seometa_desc'=>$request->$meta_desc, 
                ]);
            }
        }

        // attach category to product
        if(sizeof($request->categories_id)){
            $productToCategory = [];
            foreach ($request->categories_id as $key => $catid) {
                $productToCategory[$key]['categories_id'] = $catid;
                $productToCategory[$key]['products_id'] = $product->product_id;
            } 
            $product->products_to_categories()->attach($productToCategory);
        }

        // attch suppliere
        if(!empty($request->supplier_ids)){
            if(sizeof($request->supplier_ids)){
                $supplier_info = [];
                foreach ($request->supplier_ids as $key => $catid) {
                    $supplier_info[$key]['supplier_id'] = $catid;
                    $supplier_info[$key]['product_id'] = $product->product_id;
                } 
                $product->products_to_supplier()->attach($supplier_info);
            }
        }


        // attach product type field values
        $field_values = [];
        $product_type = ProductType::product_type_with_fields($request->product_type_id);
        if(isset($product_type->fields_group) && !empty($product_type->fields_group)){
            foreach ($product_type->fields_group as $fields_group_key => $fields_group) {   // looping fieldgroup
                
                if(isset($fields_group->fields) && !empty($fields_group->fields)){
                    foreach ($fields_group->fields as $fields_key => $fields) {     //looping all field inside 
                        
                        $fild_name = $fields->field_name;
                        array_push($field_values,[
                            'product_id'=>$product->product_id,
                            'fieldsgroup_id'=>$fields_group->fieldsgroup_id,
                            'fields_id'=>$fields->fields_id,
                            'field_name'=>$fild_name,
                            'field_value'=>$request->$fild_name
                        ]);                    
                    }                
                }

            }

        }

        $product->products_type_field_value()->attach($field_values);
        

        // attact attribute to file
        if($request->has('productAttribute')){

            if(!empty($request->productAttribute) && sizeof($request->productAttribute) > 0 ){
                foreach ($request->productAttribute as $key => $attributes) {
                    $attributes['products_id'] = $product->product_id; 
                    ProductAttribute::create($attributes);
                }
            }

        }


        if($product){
            return ApiHelper::JSON_RESPONSE(true,$field_values,'PRODUCT_CREATED');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'SOME_ISSUE');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {

        $response = Product::find($request->product_id);
        if($response !== null){
            $response->product_status = ($response->product_status == 1) ? "active":"deactive"; 
            $response->product_image = ApiHelper::getFullImageUrl($response->product_image);
            $response->productdescription_with_lang = $response->productdescription_with_lang;

            // create category tfor table
            $products_to_categories = $response->products_to_categories;
            $selected_category = [];
            if(!empty($products_to_categories)){
                foreach ($products_to_categories as $key => $cat) {
                    
                    $label_res = $cat->categorydescription()->where('categories_id', $cat->categories_id)->first();
                    $label = ($label_res !== null) ? $label_res->categories_name : '';

                    array_push($selected_category,[
                        "label"=>$label,
                        "value"=>$cat->categories_id
                    ]);        
                }
            }
            $response->selected_category = $selected_category;

            // prductType with fieldgroup
            $products_id = $response->product_id;
            $product_type = ProductType::product_type_with_fields($response->product_type_id);
            
            if(isset($product_type->fields_group)){
                $product_type = $product_type->fields_group->map(function($fields_group) use ($products_id){

                    $fields_group->fields = $fields_group->fields->map(function($fields) use ($products_id) {
                        
                        $res = ProductsTypeFieldValue::where('product_id', $products_id)->where('fields_id',$fields->fields_id)->first();
                        $fied_value = ($res !== null ) ? $res->field_value : '';

                        $fields->show_value = $fied_value;
                        return $fields;
                    });

                    return $fields_group;

                });
                // foreach ($product_type as $fields_group_key => $fields_group) {   // looping fieldgroup
                    
                //     if(isset($fields_group->fields) && !empty($fields_group->fields)){
                //         foreach ($fields_group->fields as $fields_key => $fields) {     //looping all field inside 
                            
                            // $res = Fields::where('fields_id',$fields->fields_id)->first();
                            // $fied_value = ($res !== null ) ? $res->field_value : '';

                //             $product_type[$fields_group_key][$fields_key]['show_value']  = "working"; 

                //         }                
                //     }

                // }

            }

            $response->product_type = $product_type;

            // $response->product_attribute = ProductOptions::product_options_with_value();

            $productAttribute = ProductAttribute::where('products_id', $response->product_id)->get();
            if(!empty($productAttribute)){

                foreach ($productAttribute as $key => $attribute) {
                    
                    $productAttribute[$key]['option_list'] = ProductOptions::all();
                    $productAttribute[$key]['option_value_list'] = ProductsOptionsValues::where('products_options_id', $attribute->options_id)->get();
                }
            }

            $response->productAttribute = $productAttribute;

            $response->products_type_field_value = $response->products_type_field_value;

            $response->products_to_supplier = $response->products_to_supplier;

            $selected_products_to_supplier = [];
            $selected_products_to_supplier_id = [];

            if(!empty($response->products_to_supplier )){
                foreach ($response->products_to_supplier  as $key => $supp){

                    array_push($selected_products_to_supplier_id,$supp->supplier_id);
                    array_push($selected_products_to_supplier,[
                        "label"=>$supp->supplier_name,
                        "value"=>$supp->supplier_id
                    ]);        
                }
                
            }
            $response->selected_supplier = $selected_products_to_supplier;
            $response->selected_supplier_id = $selected_products_to_supplier_id;


        }

        return ApiHelper::JSON_RESPONSE(true,$response,'');

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
            // return ApiHelper::JSON_RESPONSE(false,$request->all(),'PAGE_ACCESS_DENIED');
        


        // validation check 
        // $rules = [
        //     'categories_name.*' => 'required|string',
        //     'sort_order' => 'required',
        // ];
        // $validator = Validator::make($request->all(), $rules);
        // if ($validator->fails()) {
        //     return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());
        // }


        $products_id = $request->product_id;

        // store product 
        $saveData = $request->only(['product_model','product_condition','product_sku','product_video_url','sort_order','product_status','product_condition','product_type_id','product_stock_qty','product_price_type','product_stock_price','product_external_url','product_profit_margin','product_sale_price','product_discount_type','product_discount_amount']);

        $saveData['product_slug'] = "";

        if($request->has("product_image")){
            if(!empty($request->product_image)){
                $imgNewLoc = str_replace("tempfolder", "Product", $request->product_image);
                Storage::move($request->product_image, $imgNewLoc );
                $saveData['product_image'] = $imgNewLoc;
            }
        }

        Product::where('product_id', $products_id)->update($saveData);
        
        $product = Product::find($products_id);

        // store cat details
        foreach (Language::all() as $key => $value) {

            $name = "products_name_".$value->languages_id;
            $desc = "products_description_".$value->languages_id;
            
            if($value->languages_code == 'en'){
                $Product = Product::find($product->product_id);
                $Product->product_slug = Str::slug($request->$name);
                $Product->save();
            }

            ProductDescription::updateOrCreate([
                'products_id'=>$product->product_id,
                'languages_id'=>$value->languages_id,
            ],[
                'products_id'=>$product->product_id,
                'languages_id'=>$value->languages_id,
                'products_name'=>$request->$name,
                'products_description'=>$request->$desc,
            ]);   
        }

        // deattach category
        $product->products_to_categories()->detach();

        // attach category to product
        if(sizeof($request->categories_id)){
            $productToCategory = [];
            foreach ($request->categories_id as $key => $catid) {
                $productToCategory[$key]['categories_id'] = $catid;
                $productToCategory[$key]['products_id'] = $product->product_id;
            } 
            $product->products_to_categories()->attach($productToCategory);
        }

        // attach product type field values
        $field_values = [];
        $product_type = ProductType::product_type_with_fields($request->product_type_id);
        if(isset($product_type->fields_group) && !empty($product_type->fields_group)){
            foreach ($product_type->fields_group as $fields_group_key => $fields_group) {   // looping fieldgroup
                
                if(isset($fields_group->fields) && !empty($fields_group->fields)){
                    foreach ($fields_group->fields as $fields_key => $fields) {     //looping all field inside 
                        
                        $fild_name = $fields->field_name;
                        array_push($field_values,[
                            'product_id'=>$product->product_id,
                            'fieldsgroup_id'=>$fields_group->fieldsgroup_id,
                            'fields_id'=>$fields->fields_id,
                            'field_name'=>$fild_name,
                            'field_value'=>$request->$fild_name
                        ]);                    
                    }                
                }

            }

        }

        //old dettach 
        $product->products_type_field_value()->detach();

        // new attach
        $product->products_type_field_value()->attach($field_values);
        

        // attact attribute to file
        if($request->has('productAttribute')){

            if(!empty($request->productAttribute) && sizeof($request->productAttribute) > 0 ){
                foreach ($request->productAttribute as $key => $attributes) {
                    $attributes['products_id'] = $product->product_id; 
                    ProductAttribute::updateOrCreate([
                        'products_id'=>$product->product_id,
                        'options_id'=>$attributes['options_id'],
                        'options_values_id'=>$attributes['options_values_id'],
                    ],$attributes);
                }
            }

        }


        if($product){
            return ApiHelper::JSON_RESPONSE(true,$field_values,'PRODUCT_CREATED');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'SOME_ISSUE');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function changeStatus(Request $request){
        $api_token = $request->api_token;
        $infoData = Category::find($request->update_id);
        $infoData->status = ($infoData->status == 0) ? 1 : 0;
        $infoData->save();
        return ApiHelper::JSON_RESPONSE(true,$infoData,'STATUS_UPDATED');

    }

}
