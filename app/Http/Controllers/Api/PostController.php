<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Posts;
use App\Models\PostDescriptions;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Ecommerce\Models\SeoMeta;


use ApiHelper;



class PostController extends Controller
{


    public function index(Request $request){

            // Validate user page access
        $api_token = $request->api_token;
        $current_page = !empty($request->page) ? $request->page : 1;
        $perPage = !empty($request->perPage) ? $request->perPage : 10;
        $search = $request->search;
        $sortBY = $request->sortBy;
        $ASCTYPE = $request->orderBY;
        $language = $request->language;

        $data_query = Posts::query();

        if (!empty($search))
            $data_query = $data_query->where("slug","LIKE", "%{$search}%");

            /* order by sorting */
            if (!empty($sortBY) && !empty($ASCTYPE)) {
                $data_query = $data_query->orderBy($sortBY, $ASCTYPE);
            } else {
                $data_query = $data_query->orderBy('post_id', 'ASC');
            }

        $skip = ($current_page == 1) ? 0 : (int)($current_page - 1) * $perPage;

        $user_count = $data_query->count();

        $data_list = $data_query->skip($skip)->take($perPage)->get();

        $data_list = $data_list->map(function ($data) use ($language) {

            $cate = $data->descriptionDetails()->where('languages_id', ApiHelper::getLangid($language))->first();

            $data->post_title = ($cate == null) ? '' : $cate->post_title;
            $data->post_content = ($cate == null) ? '' : $cate->post_content;
            $data->status = ($data->status == 1) ? "active" : "deactive";
            $data->img = ApiHelper::getFullImageUrl($data->img);
            return $data;
        });


           // $data->feature_icon = ApiHelper::getFullImageUrl($data->feature_icon);


        $res = [
            'data' => $data_list,
            'current_page' => $current_page,
            'total_records' => $user_count,
            'total_page' => ceil((int)$user_count / (int)$perPage),
            'per_page' => $perPage
        ];
        return ApiHelper::JSON_RESPONSE(true, $res, '');
  

    }

    
   
    public function store(Request $request)
    {
        
           // Validate user page access
        $api_token = $request->api_token;

       
       $saveData = $request->only(['status','img']);
        $saveData['slug'] = "";
          // uplad image to live. current in temp
        ApiHelper::image_upload_with_crop($api_token,$saveData['img'], 4, 'post');

        $cat = Posts::create($saveData);

        // store post details
        foreach (Language::all() as $key => $value) {

            $post_title = "post_title_" . $value->languages_id;
            $post_content = "post_content_" . $value->languages_id;
            $post_excerpt="post_excerpt_".$value->languages_id;

            if ($value->languages_code == 'en') {

                $post_data = Posts::find($cat->post_id);
                $post_data->slug = Str::slug($request->$post_title);
                $post_data->save();
            }

          $Postdesc=PostDescriptions::create([
                'post_id' => $cat->post_id,
                'post_title' => $request->$post_title,
                'post_content' => $request->$post_content,
                'post_excerpt' => $request->$post_excerpt,
                'languages_id' => $value->languages_id,
            ]);

               if(!empty($request->$post_title) || !empty($request->$post_content)){
                SeoMeta::create([
                    'page_type'=>2,
                    'reference_id'=>$Postdesc->post_description,
                    'language_id'=>$value->languages_id,
                    'seometa_title'=>$request->$post_title,
                    'seometa_desc'=>$request->$post_content, 
                ]);
            }
        }

        if ($cat) {
            return ApiHelper::JSON_RESPONSE(true, [], 'POST_CREATED');
        } else {
            return ApiHelper::JSON_RESPONSE(false, [], 'SOME_ISSUE');
        }

    }

    public function edit(Request $request)
    {
    
        $api_token = $request->api_token;
        $data_list = Posts::with('descriptionDetails')->find($request->post_id);
        return ApiHelper::JSON_RESPONSE(true,$data_list,'');

    }

    public function update(Request $request)
    { 

           // Validate user page access
        $api_token = $request->api_token;
        $post_id = $request->post_id;

        // store pages
        $saveData = $request->only(['status','img']);
            $saveData['slug'] = "";
      ApiHelper::image_upload_with_crop($api_token,$saveData['img'], 4, 'post');
        $cat = Posts::where('post_id', $post_id)->update($saveData);

        // store page description

        // detach info
        PostDescriptions::where('post_id', $post_id)->delete();

        foreach (Language::all() as $key => $value) {

            $post_title = "post_title_" . $value->languages_id;
            $post_content = "post_content_" . $value->languages_id;
            $post_excerpt="post_excerpt_".$value->languages_id;



            if ($value->languages_code == 'en') {
                $page_data = Posts::find($post_id);
                $page_data->slug = Str::slug($request->$post_title);
                $page_data->save();
            }

           $Postdes=PostDescriptions::create([
                'post_id' => $post_id,
                'post_title' => $request->$post_title,
                'post_content' => $request->$post_content,
                'post_excerpt' => $request->$post_excerpt,
                'languages_id' => $value->languages_id,
            ]);


            if(!empty($request->$post_title) || !empty($request->$post_content)){
                // create new
                SeoMeta::updateOrCreate(['page_type'=>2,
                        'reference_id'=>$Postdes->post_description,
                        'language_id'=>$value->languages_id ,
                        ],[
                        'seometa_title'=>$request->$post_title,'seometa_desc'=>$request->$post_content 
                    ]);
            }

        }
        if ($cat) {
            return ApiHelper::JSON_RESPONSE(true, $saveData, 'POST_UPDATED');
        } else {
            return ApiHelper::JSON_RESPONSE(false, [], 'SOME_ISSUE');
        }
            




    }


    public function destroy(Request $request)
    {
        $api_token = $request->api_token;

        $status = Posts::where('post_id',$request->post_id)->delete();
        if($status) {
            return ApiHelper::JSON_RESPONSE(true,[],'POST_DELETED');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'NOT_POST_DELETED');
        }
    }

    public function changeStatus(Request $request)
    {

        $api_token = $request->api_token; 
        $post_id = $request->post_id;
        $sub_data = Posts::find($post_id);
        $sub_data->status = ($sub_data->status == 0 ) ? 1 : 0;         
        $sub_data->save();
        
        return ApiHelper::JSON_RESPONSE(true,$sub_data,'POST_STATUS');
    }


}
