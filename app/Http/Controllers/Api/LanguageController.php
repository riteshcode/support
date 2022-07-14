<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Language;
use App\Models\Translation;
use App\Models\TranslationKey;
use Illuminate\Http\Request;
use ApiHelper;
use App\Models\ModuleSection;


class LanguageController extends Controller
{
    public function index_all(Request $request){

        $api_token = $request->api_token;

        $language_list = Language::where('status','1')->get();

        return ApiHelper::JSON_RESPONSE(true,$language_list,'');
    }
    //This Function is used to show the list of languages
    public function index(Request $request)
    { 
       $current_page = !empty($request->page)?$request->page:1;
        $perPage = !empty($request->perPage)?$request->perPage:10;
        $search = $request->search;
        $sortBy = $request->sortBy;
        $orderBy = $request->orderBy;
        /*Fetching language data*/ 
        $languages_query = Language::select('languages_id', 'languages_name', 'languages_code', 'languages_icon', 'text_align', 'is_default', 'status');


        /* order by sorting */
        if(!empty($sortBy) && !empty($orderBy))
            $languages_query = $languages_query->orderBy($sortBy,$orderBy);
        else
            $languages_query = $languages_query->orderBy('languages_id','DESC');

        $skip = ($current_page == 1)?0:(int)($current_page-1)*$perPage;

        $languages_count = $languages_query->count();

        $languages_list = $languages_query->skip($skip)->take($perPage)->get();

        $languages_list = $languages_query->get();

        $languages_list = $languages_list->map(function($language_status){
            if($language_status->status=='0'){
                $language_status->status = 'Deactive';
            }else{
                $language_status->status = 'Active';
            }
            return $language_status;
        });  

        $translation = [];
        foreach ($languages_list as $key => $value) {
            $value->translation = $value->translation;
            $translation[$key] = $value;
        }
        /* Fetching data of translation */
        $languages_list = $translation;
        /*Binding data into a variable*/
        

        $res = [
            'data'=>$languages_list,
            'current_page'=>$current_page,
            'total_records'=>$languages_count,
            'total_page'=>ceil((int)$languages_count/(int)$perPage),
            'per_page'=>$perPage,
        ];
        /*returning data to client side*/
        return ApiHelper::JSON_RESPONSE(true,$res,'');
    }
    
    //This Function is used to show the particular language data
    public function edit(Request $request){
        /*fetching data from api*/
        $languages_id = $request->languages_id;
        /*fetching data of language*/
        $data = Language::where('languages_id', $languages_id)->first();
        /*returning data to client side*/
        return ApiHelper::JSON_RESPONSE(true,$data,'');
    }
    
    //This Function is used to update the particular language data
    public function update(Request $request){
        /*fetching data from api*/
        $languages_id = $request->languages_id;
        $languages_name = $request->languages_name;
        $languages_code = $request->languages_code;
        $languages_icon = $request->languages_icon;
        /*validating data*/
        $validator = Validator::make($request->all(),[
            'languages_name' => 'required',
            'languages_code' => 'required',
        ],[
            'languages_name.required'=>'LANGUAGE_NAME_REQUIRED',
            'languages_code.required'=>'LANGUAGE_CODE_REQUIRED',
        ]);
        /*if validation fails then it will show error message*/
        if ($validator->fails()) {
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());
        }
        /*updating language data after validation*/
        $data = Language::where('languages_id', $languages_id)->update(['languages_name'=>$languages_name, 'languages_code'=>$languages_code, 'languages_icon'=>$languages_icon]);
        /*returning something to client side*/
        return ApiHelper::JSON_RESPONSE(true,$data,'SUCCESS_LANGUAGE_UPDATE');
    }
    
    //This Function is used to get the details of language
    public function details(Request $request){
        /*fetching data from api*/
        $languages_code = $request->languages_code;
        $lang_key = $request->lang_key; 
        /*fetching translation data*/
        $language_data = Language::where('languages_code',$languages_code)->first();
        if(!empty($language_data->languages_id)){
            $data = Translation::where('languages_id', $language_data->languages_id)->where('lang_key', $lang_key)->first();
        }else{
            $data = Translation::where('languages_id', '1')->where('lang_key', $lang_key)->first();
        }
        /*checking if translation data is not empty*/

        if(!empty($data)){
            /*fetching language data*/
            $data->lang = $data->language->lang_value;
        }
        /*returning something to client side*/
        return ApiHelper::JSON_RESPONSE(true,$data,'');
    }
    
    //This Function is used to add language
    public function store(Request $request)
    {
        /*fetching data from api*/
        $languages_name = $request->languages_name;
        $languages_code = $request->languages_code;  
        /*validating data*/ 
        $validator = Validator::make($request->all(),[
            'languages_name' => 'required',
            'languages_code' => 'required',
        ],[
            'languages_name.required'=>'LANGUAGE_NAME_REQUIRED',
            'languages_code.required'=>'LANGUAGE_CODE_REQUIRED',
            'languages_code.unique'=>'LANGUAGE_CODE_UNIQUE',
        ]);
        /*if validation fails then it will show error message*/
        if ($validator->fails()) {
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());
        }
        /*adding language and picking last inserted id*/
        $data = Language::insertGetId([
            'languages_name'=>$languages_name, 
            'languages_code'=>$languages_code,
            'languages_icon'=>$request->languages_icon,
            'text_align'=>$request->text_align,
            'is_default'=>$request->is_default,
        ]);
        
        if($data){
            return ApiHelper::JSON_RESPONSE(true,$data,'SUCCESS_LANGUAGE_ADD');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_LANGUAGE_ADD');
        }
    }
    
    //This Function is used to delete language
    public function delete(Request $request)
    {
        /*fetching data from api*/
        $languages_id = $request->languages_id;
        /*deleting language on the basis of id*/
        $data = Language::where('languages_id',$languages_id)->delete();
        /*deleting translation on the basis of id*/
        $translation_data = Translation::where('languages_id',$languages_id)->delete();
        if($translation_data){
            /*returning something to client side*/
            return ApiHelper::JSON_RESPONSE(true,$data,'SUCCESS_LANGUAGE_DELETE');
        }
    }

    public function changeStatus(Request $request)
    {

        $api_token = $request->api_token;
        
        $languages_id = $request->languages_id;
        $sub_data = Language::where('languages_id', $languages_id)->first();
       
        if($sub_data->status =='1'){
            $data = Language::where('languages_id', $languages_id)->update(['status'=> '0']);
        }else{
            $data = Language::where('languages_id', $languages_id)->update(['status'=> '1']);
        }
        
        return ApiHelper::JSON_RESPONSE(true,$data,'SUCCESS_STATUS_UPDATE');
    }

    //This Function is used to show the particular translation data
    public function trans_edit(Request $request){
        /*fetching data from api*/
        $languages_id = $request->languages_id;
        $lang_key = $request->lang_key;
        /*fetching translation data on the basis of language id and language key*/
        $data = Translation::where('languages_id', $languages_id)->where('lang_key', $lang_key)->first();
        /*checking if translation data is not empty*/
        if(!empty($data)){
            /*fetching language data*/
            $data->language = $data->language;
        }
        /*returning something to client side*/
        return ApiHelper::JSON_RESPONSE(true,$data,'');
    }

    public function trans_add_key(Request $request){

        if(strpos($request->lang_key, ",")){
            $langkey = explode(',', $request->lang_key);
            foreach ($langkey as $key => $lang) {
                if(!empty($lang)){
                    TranslationKey::updateOrCreate(['translation_key' => trim($lang)],
                        [
                        'section_id'=> $request->section_id == null ? 0 : $request->section_id,
                        'translation_key' => trim($lang),
                        'source'=>$request->source,

                        ]
                    );
                }

            }
        }else
        {
            TranslationKey::updateOrCreate(
                ['translation_key' => trim($request->lang_key)],
                ['section_id'=> $request->section_id == null ? 0 : $request->section_id,'translation_key' => trim($request->lang_key),

                 'source'=>$request->source,
            ]
            );


        }

        return ApiHelper::JSON_RESPONSE(true,[],'SUCCESS_KEYS_ADD');
    }

     //This Function is used to get the details of language
    public function trans_edit_key_value(Request $request){
        $all_key_value = [];

        $languages_id = $request->languages_id;
        $lang_key = $request->lang_key; 
        
        $languageInfo = Language::where('languages_code',$languages_id)->first();

        if($languageInfo == null) return ApiHelper::JSON_RESPONSE(false,[],'LANGUAGE_NOT_FOUND');
       
        // get current language all json_value
        $all_key_value = $languageInfo->translation()->select('lang_value')->where(['lang_key'=>$lang_key])->first();

        if($all_key_value === null)     $all_key_value = [];
        else{

            $all_key_value = $all_key_value->lang_value;    
            $all_key_value = (array)json_decode($all_key_value);
            
        }


        $lang_key_id = ($lang_key == 'backend') ? 1 : ( ( $lang_key == 'website' ) ? 2 : 3) ;       // get lang_key id

        // all key group by section_id 
        $all_key_group = TranslationKey::selectRaw(" distinct(section_id) as section_id ")
        ->where('source', $lang_key_id)->orderBy('section_id', 'ASC')->get();
        
        $all_key_group_value_pair = [];

        if(!empty($all_key_group)){

            foreach ($all_key_group as $key => $group) {
                $section = ModuleSection::find($group->section_id);

                // get section_id wise key list
                $all_translation_key = TranslationKey::select("translation_key")->where('source', $lang_key_id)->where('section_id', $group->section_id)->get();

                $all_trans_key_val_pair = [];

                if(!empty($all_translation_key)){

                    // looping key to get value from abover var.
                    foreach ($all_translation_key as $key => $trans_key) {
                        if(!empty($all_key_value)){
                            $all_trans_key_val_pair[$trans_key->translation_key] = 
                                array_key_exists($trans_key->translation_key, $all_key_value) 
                                ? $all_key_value[$trans_key->translation_key] : "";
                        }else{
                            $all_trans_key_val_pair[$trans_key->translation_key] = '';
                        }
                    }
                }

                // set all_key_value to section_group
                $all_key_group_value_pair[$key]['all_trans_key_val_pair'] = $all_trans_key_val_pair;


                if($group->section_id == 0)
                    $all_key_group_value_pair[$key]['group_name'] = "Default";
                else
                    $all_key_group_value_pair[$key]['group_name'] = !empty($section) ? $section->section_name : '';
                
            }

        }

        // return ApiHelper::JSON_RESPONSE(true,$all_key_group_value_pair,'');

        $res = [
            'languages_id'=>$languageInfo->languages_id,
            'lang_key'=>$lang_key,
            'group_key'=>(array)$all_key_group_value_pair,
        ];

        return ApiHelper::JSON_RESPONSE(true,$res,'');
    }

    //This Function is used to update the particular translation data
    public function trans_update(Request $request){
        
        $langInfo = Language::where('languages_code',$request->language_code)->first();

        $inst = $request->except(['api_token']);
        
        $check = $request->only(['language_code','lang_key']);
        
        // attach lang_id
        if(!empty($langInfo)){
            $inst['languages_id'] = $langInfo->languages_id;
            $check['languages_id'] = $langInfo->languages_id;
                
        }


        $inst['lang_value'] = json_encode($inst['lang_value']);

        $res = Translation::updateOrCreate($check,$inst);
        if($res) return ApiHelper::JSON_RESPONSE(true,$res,'SUCCESS_TRANSLATION_UPDATE');
        else return ApiHelper::JSON_RESPONSE(false,$res,'ERROR_TRANSLATION_UPDATE');
        
    }

    public function trans_delete_key(Request $request){
        $res = TranslationKey::where('translation_key',$request->translation_key)->delete();
        return ApiHelper::JSON_RESPONSE(true,$res,'SUCCESS_KEY_DELETE');
    }
    
}
