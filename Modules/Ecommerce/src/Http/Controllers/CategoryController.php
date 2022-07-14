<?php
namespace Modules\Ecommerce\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


use Modules\Ecommerce\Models\CategoryDescription;
use Modules\Ecommerce\Models\Category;

use App\Models\Language;
use ApiHelper;

use Illuminate\Support\Facades\Storage;



class CategoryController extends Controller
{

    public $page = 'categories';
    public $pageview = 'view';
    public $pageadd = 'add';
    public $pagestatus = 'remove';
    public $pageupdate = 'update';


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create_sub_category(Request $request){
        $language = $request->language;

        $res = [];

        $cat = Category::where('parent_id',0)->get();
        $cat = $cat->map(function($data) use ($language)  {
            $cate = $data->categorydescription()->where('languages_id', ApiHelper::getLangid($language))->first();
            $data->category_name = ($cate == null) ? '' : $cate->categories_name;

            // getting sub category
            $sub_category = Category::where('parent_id',$data->categories_id)->get();
            if(sizeof($sub_category) >  0){
                $sub_category = $sub_category->map(function($sub) use ($language) {
                    $subcat = $sub->categorydescription()->where('languages_id', ApiHelper::getLangid($language))->first();
                    $sub->category_name = ($subcat == null) ? '' : $subcat->categories_name;
                    return $sub;
                });
            }
            $data->sub_category = $sub_category;

            return $data;
        });
        $res['category'] = $cat;
        $res['language'] = Language::all();

        return ApiHelper::JSON_RESPONSE(true,$res,'');

    }

    public function index(Request $request)
    {
        // Validate user page access
        $api_token = $request->api_token;

        // if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageview))
        //     return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        


        $current_page = !empty($request->page)?$request->page:1;
        $perPage = !empty($request->perPage)?$request->perPage:10;
        $search = $request->search;
        $sortBY = $request->sortBy;
        $ASCTYPE = $request->orderBY;
        $language = $request->language;
        
        $data_query = Category::where('parent_id',0);
        
        if(!empty($search))
            // $data_query = $data_query->where("name","LIKE", "%{$search}%")->orWhere("email", "LIKE", "%{$search}%");

            /* order by sorting */
        if(!empty($sortBY) && !empty($ASCTYPE)){
            $data_query = $data_query->orderBy($sortBY,$ASCTYPE);
        }else{
            $data_query = $data_query->orderBy('categories_id','ASC');
        }

        $skip = ($current_page == 1)?0:(int)($current_page-1)*$perPage;
        
        $user_count = $data_query->count();

        $data_list = $data_query->skip($skip)->take($perPage)->get();

        $data_list = $data_list->map(function($data) use ($language)  {

            $cate = $data->categorydescription()->where('languages_id', ApiHelper::getLangid($language))->first();

            $data->category_name = ($cate == null) ? '' : $cate->categories_name;
            // $data->description = ($cate == null) ? '' : $cate->categories_description;
            $data->status = ($data->status == 1) ? "active":"deactive"; 
            $data->categories_image = ApiHelper::getFullImageUrl($data->categories_image);

            // getting sub category
            $sub_category = Category::where('parent_id',$data->categories_id)->get();
            if(sizeof($sub_category) >  0){
                $sub_category = $sub_category->map(function($sub) use ($language) {

                    $subcat = $sub->categorydescription()->where('languages_id', ApiHelper::getLangid($language))->first();
                    $sub->category_name = ($subcat == null) ? '' : $subcat->categories_name;
                    $sub->status = ($sub->status == 1) ? "active":"deactive"; 
                    $sub->categories_image = ApiHelper::getFullImageUrl($sub->categories_image);


                        // getting sub sub category
                        $sub_sub_category = Category::where('parent_id',$sub->categories_id)->get();
                        if(sizeof($sub_sub_category) >  0){
                            $sub_sub_category = $sub_sub_category->map(function($sub) use ($language) {
                                $subcat = $sub->categorydescription()->where('languages_id', ApiHelper::getLangid($language))->first();
                                $sub->category_name = ($subcat == null) ? '' : $subcat->categories_name;
                                $sub->status = ($sub->status == 1) ? "active":"deactive"; 
                                $sub->categories_image = ApiHelper::getFullImageUrl($sub->categories_image);
                                return $sub;
                            });
                        }
                        $sub->sub_sub_category = $sub_sub_category;


                    return $sub;
                });
            }
            $data->sub_category = $sub_category;


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
     */
    public function create()
    {
        //
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
        $saveData = $request->only(['sort_order','status','parent_id']);
        // $saveData['parent_id'] = ($request->category_type == 'parent') ? $request->main_category : $request->sub_category;
        
        
        $saveData['categories_slug'] = "";

        if($request->has("categories_image")){
            if(!empty($request->categories_image)){
                $imgNewLoc = str_replace("tempfolder", "Category", $request->categories_image);
                Storage::move($request->categories_image, $imgNewLoc );
                $saveData['categories_image'] = $imgNewLoc;
            }
        }

        $cat = Category::create($saveData);

        // store cat details
        foreach (Language::all() as $key => $value) {

            $cat_name = "categories_name_".$value->languages_id;
            $desc = "categories_name_".$value->languages_id;
            $title = "categories_title_".$value->languages_id;
            $categories_meta_desc = "categories_meta_desc_".$value->languages_id;

            if($value->languages_code == 'en'){
                $category = Category::find($cat->categories_id);
                $category->categories_slug = Str::slug($request->$cat_name);
                $category->save();
            }

            CategoryDescription::create([
                'categories_id'=>$cat->categories_id,
                'categories_name'=>$request->$cat_name,
                'categories_description'=>$request->$desc,
                'languages_id'=>$value->languages_id,
                'categories_title'=>$title,
                'categories_meta_desc'=>$request->$categories_meta_desc,
            ]);   
        }

        if($cat){
            return ApiHelper::JSON_RESPONSE(true,[],'CATEGORY_CREATED');
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
        $response = Category::find($request->categories_id);

        if($response != null)
            $response->categorydescription = $response->categorydescription;

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
        $categories_id = $request->categories_id;

        // if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageadd))
        //     return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        


        // validation check 
        // $rules = [
        //     'categories_id' => 'required',
        //     'sort_order' => 'required',
        // ];
        // $validator = Validator::make($request->all(), $rules);
        // if ($validator->fails()) 
        //     return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());
        

        // store category 
        $saveData = $request->only(['sort_order','status','parent_id']);
        
        // check if new category image uploaded than move from tempfile
        if($request->has("categories_image")){
            
            $imgExp = explode("/", $request->categories_image);

            if($imgExp[0] ==  "tempfolder"){
                $imgNewLoc = str_replace("tempfolder", "Category", $request->categories_image);
                Storage::move($request->categories_image, $imgNewLoc );
                $saveData['categories_image'] = $imgNewLoc;   
            }
        }

        
        $cat = Category::where('categories_id', $categories_id)->update($saveData);

        // store cat details
        foreach (Language::all() as $key => $value) {

            $cat_name = "categories_name_".$value->languages_id;
            $desc = "categories_name_".$value->languages_id;
            $title = "categories_title_".$value->languages_id;
            $categories_meta_desc = "categories_meta_desc_".$value->languages_id;

            $categories_description_id = "categories_description_id_".$value->languages_id;

            if($value->languages_code == 'en'){
                $category = Category::find($categories_id);
                $category->categories_slug = Str::slug($request->$cat_name);
                $category->save();
            }
            $res = CategoryDescription::where('categories_description_id', $request->$categories_description_id)->update([
                'categories_name'=>$request->$cat_name,
                'categories_description'=>$request->$desc,
                'categories_title'=>$request->$title,
                'categories_meta_desc'=>$request->$categories_meta_desc,
            ]); 

        }
        if($cat){
            return ApiHelper::JSON_RESPONSE(true,$saveData,'CATEGORY_UPDATED');
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
