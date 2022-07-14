<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SuperUser;
use App\Models\Subscriber;
use App\Models\Subscription;
use App\Models\UserBusiness;
use App\Models\Module;
use App\Models\ModuleSection;
use App\Models\Industry;
use ApiHelper;
use Illuminate\Support\Facades\Storage;
use App\Mail\AutoGeneratePassword;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendAutoGeneratePasswordMail;
use Illuminate\Support\Facades\Config;
use App\Events\LoginEvent;
use Modules\HRM\Models\Staff;

class UserController extends Controller
{
    public $page = 'user';
    public $pageview = 'view';
    public $pageadd = 'add';
    public $pagestatus = 'remove';
    public $pageupdate = 'update';


    /* login through api */
    public function login(Request $request){
        if($request->has('email') && $request->has('password')){

            if(base64_decode($request->loginType) == 'administrator'){
                $userType = "administrator";
                $db_id = 0;
                $industry_id = 0;
                ApiHelper::essential_config_regenerate($db_id);
            }else{
                $userBus = UserBusiness::where('users_email', $request->email)->first();
                $userType = "subscriber";
                
                if($userBus == null)
                    return ApiHelper::JSON_RESPONSE(false,[],'NOT_VALID_URL');

                $db_id = $userBus->subscription_db_suffix;
                
                ApiHelper::essential_config_regenerate($db_id);


                $subs = Subscription::where('subscription_id', $userBus->subscription_id)->first();
                if($subs != null)
                    $industry_id = $subs->industry_id;
                else
                    $industry_id = 0;
            }

            

            $res = User::where('email',$request->email)->first();

            if($res != null){
                /* check account active or not */
                if($res->status == 0)
                    return ApiHelper::JSON_RESPONSE(false,[],'CONTACT_ADMIN');

                /* passowrd match */
                if(Hash::check($request->password, $res->password)){


                    $loginHistorys = [
                        "user_id" =>$res->id,
                        "user_ip"=>$request->ip(),
                        "location" => "",
                        "browser" =>$request->header("Browser"),
                        "os"=>"",
                        "longitude"=>$request->header("Longitude"),
                        "latitude"=>$request->header("Latitude"),
                        "city"=>"",
                        "country_id"=>""
                    ]; 
                    $loginHistory = LoginEvent::dispatch($loginHistorys);

                    $response = [
                        'userType' => $userType,
                        'db_control' => $db_id,
                        'role'=>ApiHelper::get_role_from_token($res->api_token),
                        'permission'=>ApiHelper::get_permission_list($res->api_token),
                        'user'=> $res,
                        'business_id'=>UserBusiness::where('users_email', $request->email)->first(),
                        // 'Longitude'=>ApiHelper::user_login_history($request->header("Longitude")),
                        'ModuleSectionList'=>ApiHelper::ModuleSectionList($request->header("Longitude")),
                        'history'=>$loginHistory,
                        'loginHistorys' => $loginHistorys,
                        'languageInfo'=>ApiHelper::getLanguageAndTranslation($res->api_token),
                        'settingInfo'=>ApiHelper::getSettingInfo($res->api_token),

                    ];

                    $sideMenu = $this->getModuleListForSideMenu($userType, $industry_id);
                    $response['module_list'] = $sideMenu['module_list'];
                    $response['quick_list'] = $sideMenu['quick_list'];

                    // login history event
                    return ApiHelper::JSON_RESPONSE(true,$response,'LOGIN_SUCCESS');

                }else{
                    return ApiHelper::JSON_RESPONSE(false,[],'WRONG_PASSWORD');
                }
            }else{
                return ApiHelper::JSON_RESPONSE(false,[],'WRONG_EMAIL');
            }

        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'EMAIL_PASSWORD_MISSING');
        }
    }

    public function getModuleListForSideMenu($user_type, $industry_id){
        $returnItem = [];
        $quickInc = 0;
        $selectionItem = [];
        if($user_type == 'administrator'){
            $module_list = Module::where('status',1)->where('access_priviledge',0)->orderBy('sort_order','ASC')->get();
            // $module_list = Module::where('status',1)->where('access_priviledge',0)->orWhere('access_priviledge',1)->orderBy('sort_order','ASC')->get();
            foreach ($module_list as $mkey => $module) {
                $module_section = ModuleSection::where('module_id',$module->module_id)->where('status',1)->where('parent_section_id','0')->orderBy('sort_order','ASC')->get();
                foreach ($module_section as $skey => $section) {
                    $module_section[$skey]['submenu'] = ModuleSection::where('parent_section_id',$section['section_id'])->where('status',1)->orderBy('sort_order','ASC')->get();
                } 
                $module_list[$mkey]['menu'] = $module_section; 
                // module wise quicklist view
                $quick_section = ModuleSection::where('module_id',$module->module_id)->where('status',1)->where('quick_access','1')->get();
                if(sizeof($quick_section) > 0 ){
                    foreach ($quick_section as $key => $value) {
                        $selectionItem[$quickInc] = $value;
                        $quickInc++;   
                    } 
                }
            }
            $returnItem['module_list'] = $module_list;
            $returnItem['quick_list'] = $selectionItem;
        }else{
            $insutry = Industry::find($industry_id);
            $module_list_item = [];
            if(!empty($insutry->modules)){
                $module_list = $insutry->modules()->orderBy('sort_order','ASC')->get();
                foreach ($module_list as $mkey => $module) {
                    $module_section = ModuleSection::where('status',1)->where('module_id',$module->module_id)->where('parent_section_id','0')->orderBy('sort_order','ASC')->get();
                    foreach ($module_section as $skey => $section) {
                        $module_section[$skey]['submenu'] = ModuleSection::where('status',1)->where('parent_section_id',$section['section_id'])->orderBy('sort_order','ASC')->get();
                    } 
                    $module_list[$mkey]['menu'] = $module_section; 
                    // module wise quicklist view
                    $quick_section = ModuleSection::where('status',1)->where('module_id',$module->module_id)->where('quick_access','1')->get();
                    if(sizeof($quick_section) > 0 ){
                        foreach ($quick_section as $key => $value) {
                            $selectionItem[$quickInc] = $value;
                            $quickInc++;   
                        } 
                    }
                }
                $module_list_item = $module_list;
            }
            $returnItem['module_list'] = $module_list_item;
            $returnItem['quick_list'] = $selectionItem;
        }
        return $returnItem;
    }


    /* get all userlist */
    public function index(Request $request)
    {
        // Validate user page access
        $api_token = $request->api_token;
        // return ApiHelper::JSON_RESPONSE(false,ApiHelper::generate_random_token('token',20),'PAGE_ACCESS_DENIED');
        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageview)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }
        $current_page = !empty($request->page)?$request->page:1;
        $perPage = !empty($request->perPage)?$request->perPage:10;
        $search = $request->search;
        $sortBY = $request->sortBy;
        $ASCTYPE = $request->orderBY;

        $user_query = User::where('created_by',ApiHelper::get_adminid_from_token($api_token));

        if(!empty($search))
            $user_query = $user_query
        ->where("first_name","LIKE", "%{$search}%")
        ->where("last_name","LIKE", "%{$search}%")
        ->orWhere("email", "LIKE", "%{$search}%");

        /* order by sorting */
        if(!empty($sortBY) && !empty($ASCTYPE)){
            $user_query = $user_query->orderBy($sortBY,$ASCTYPE);
        }else{
            $user_query = $user_query->orderBy('id','ASC');
        }

        $skip = ($current_page == 1)?0:(int)($current_page-1)*$perPage;

        $user_count = $user_query->count();

        $user_list = $user_query->skip($skip)->take($perPage)->get();

        $user_list = $user_list->map(function($user){
            $user->full_image_path = Storage::path($user->profile_photo);
            $user->role_name = ApiHelper::get_role_from_token($user->api_token);
            $user->name = $user->first_name.' '.$user->last_name;
            if($user->status == '1'){
                $user->status = 'Active';
            }else{
                $user->status = 'Deactive';
            }   
            return $user;
        });

        $res = [
            'data'=>$user_list,
            'current_page'=>$current_page,
            'total_records'=>$user_count,
            'total_page'=>ceil((int)$user_count/(int)$perPage),
            'per_page'=>$perPage
        ];
        return ApiHelper::JSON_RESPONSE(true,$res,'');
    }


    /* create user and assign role  */
    public function store(Request $request)
    {

        // Validate user page access
        $api_token = $request->api_token;
        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageadd)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }

        // validation check
        /*$rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
            'role_name'=> 'required',
        ];*/
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
            'role_name'=> 'required',
        ],[
            'name.required'=>'NAME_REQUIRED',
            'name.max'=>'NAME_MAX',
            'email.required'=>'EMAIL_REQUIRED',
            'email.email'=>'EMAIL_EMAIL',
            'password.required'=>'PASSWORD_REQUIRED',
            'password.min'=>'PASSWORD_MIN',
            'role.required'=>'ROLE_NAME_REQUIRED',
        ]);
        if ($validator->fails()) {
            return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());
        }
        /*$validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails())
        return ApiHelper::JSON_RESPONSE(false,[],$validator->messages());*/
        

        // store user and assign role
        $user = User::create([
            'first_name'=>$request->name,
            'last_name'=>'',
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'created_by'=>ApiHelper::get_adminid_from_token($request->api_token),
            'api_token'=>Hash::make($request->name),
        ]);
        // attach role
        $user->roles()->attach($request->role_name);

        // attach department
        // if($request->has('department_id')){
        // Staff::create([
        //     'employee_id'=>'emp'.rand(10,99).rand(10,99).rand(10,99),
        //     'user_id'=>$user->id,
        //     'department_id'=>$request->department_id,
        //     'gender'=>$request->gender,
        //     'date_of_joining'=>$request->date_of_joining,
        //     'date_of_leaving'=>$request->date_of_leaving,
        //     'marital_status'=>$request->marital_status,
        //     'date_of_birth'=>$request->date_of_birth,
        //     'state'=>$request->state,
        //     'city'=>$request->city,
        //     'zipcode'=>$request->zipcode,
        //     'contact_no'=>$request->contact_no,
        //     'address'=>$request->address,
        //     'salary'=>'',
        //     'created_by'=>ApiHelper::get_adminid_from_token($request->api_token),
        //     'update_by' => ApiHelper::get_adminid_from_token($request->api_token),
        // ]);
        // }

        //if user is subscriber replica of user store in userbusiness
        if($request->has('userType') && $request->userType == 'subscriber'){
            $parent_id = ApiHelper::get_adminid_from_token($request->api_token);
            $userBusiness = UserBusiness::where('users_id', $parent_id)->first();
            $newBusinnens = $userBusiness->replicate();
            
            $ubs = UserBusiness::orderBY('users_business_id','DESC')->first();
            if($ubs == null)
                $last_business_id = 1;
            else
                $last_business_id = (int)$ubs->users_business_id+1;

            $newBusinnens->users_business_id = $last_business_id; 
            $newBusinnens->users_id = $user->id;
            $newBusinnens->users_email = $user->email;
            $newBusinnens->parent_id = $parent_id;
            $newBusinnens->date_added = date('Y-m-d h:s:i');
            $newBusinnens->save();

        }

        if($user)
            return ApiHelper::JSON_RESPONSE(true,[],'SUCCESS_USER_ADD');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_USER_ADD');
        
    }

    public function edit(Request $request)
    {
        // Validate user page access
        $api_token = $request->api_token;
        
        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageupdate)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }

        $userdetail = User::find($request->user_id);
        if($userdetail != null){

           /* $userdetail->full_image_path = asset('storage/'.$userdetail->image_path);*/
              $userdetail->full_image_path = (!empty($userdetail->profile_photo))?Storage::url($userdetail->profile_photo):'';
            if(isset($userdetail->roles[0]))
                $userdetail->role_name = $userdetail->roles[0]->name;
            else
                $userdetail->role_name = '';

            return ApiHelper::JSON_RESPONSE(true,$userdetail,'');
        }else
        return ApiHelper::JSON_RESPONSE(false,[],'SOMETHING_WRONG');
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
        // return ApiHelper::JSON_RESPONSE(true,$request->file('profileimg'),'Profile updated successfully !');
                // Validate user page access
        $api_token = $request->api_token;
        $user_id = $request->user_id;

        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pageupdate)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }

        $updateData = $request->only('first_name', 'last_name', 'email');
        if($request->has('password') && !empty($request->password))
            $updateData['password'] = Hash::make($request->password);

        if($request->has("profileimg")){
            if($request->file('profileimg')){

                $updateData['profile_photo'] =  $request->file('profileimg')->store('user');
            }
        }
        $userInfo  =  User::find($user_id);

        $autoGenPass = rand();
        if($request->has('autoGenerate')){
            if($request->autoGenerate == 'on'){

                $updateData['password'] = Hash::make($autoGenPass);
                // sent auto generate password to mail
                // Mail::to($userInfo->email)->queue(new AutoGeneratePassword($autoGenPass));
                $arralist = [
                    'email'=>$userInfo->email,
                    'autoGenPass'=>$autoGenPass
                ];
                dispatch(new SendAutoGeneratePasswordMail($arralist));

            }
        }


        $status = $userInfo->update($updateData);

        if($request->has('role_name') && !empty($request->role_name)){
            $user = User::find($user_id);
            $user->roles()->detach();
            $user->roles()->attach($request->role_name);
        }

        $userInfo  =  User::find($user_id);

        if($status)
            return ApiHelper::JSON_RESPONSE(true,$userInfo,'SUCCESS_PROFILE_UPDATE');
        else
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_PROFILE_UPDATE');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $api_token = $request->api_token;
        $id = $request->deleteId;
        
        if(!ApiHelper::is_page_access($api_token, $this->page, $this->pagestatus)){
            return ApiHelper::JSON_RESPONSE(false,[],'PAGE_ACCESS_DENIED');
        }

        $status = User::destroy($id);
        if($status) {
            return ApiHelper::JSON_RESPONSE(true,[],'SUCCESS_USER_DELETE');
        }else{
            return ApiHelper::JSON_RESPONSE(false,[],'ERROR_USER_DELETE');
        }
    }

    /*
        forget password
     */
        public function forgetPassword(Request $request){
            if($request->has('email')){
                $res = User::where('email',$request->email)->first();
                if($res != null){
                    /* check account active or not */
                    if($res->status == 'deactive'){
                        return ApiHelper::JSON_RESPONSE(false,[],'CONTACT_ADMIN');
                    }
                    /* passowrd match */
                    $res->password = Hash::make("password");
                    $res->save();
                    return ApiHelper::JSON_RESPONSE(true,[],'SUCCESS');

                }else{
                    return ApiHelper::JSON_RESPONSE(false,[],'INVALID_EMAIL');
                }

            }else{
                return ApiHelper::JSON_RESPONSE(false,[],'EMAIL_MISSING');
            }
        }


    }
