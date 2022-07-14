<?php
namespace App\Helper;

use App\Models\User;
use App\Models\UserBusiness;
use Modules\Department\Models\Role;
use Modules\Department\Models\Permission;
use App\Models\Notification;
use App\Models\NotificationMessage;
use DB;
use App\Models\ModuleSection;

use App\Models\Language;
use App\Models\Translation;
use App\Models\TranslationKey;
use App\Models\Setting;

use Illuminate\Support\Facades\Storage;
use App\Models\TempImages;
use Image;
use File;



class ApiHelper
{

	/*
        api token validate
    */
        public static function api_token_validate($token){
            $token_decodeval = base64_decode($token);
            $token_ary = explode('_DB', $token_decodeval);


            Self::essential_config_regenerate($token_ary[1]);

            $user = User::where('api_token', $token_ary[0])->first();
            return !empty($user)?true:false;
        }

        public static function get_api_token($token){
            $token_decodeval = base64_decode($token);
            $token_ary = explode('_DB', $token_decodeval);

            if(isset($token_ary[0]))
                return $token_ary[0];
            else
                return null;
        }
    /*
        check user_id has parent or not
    */
        public static function get_parent_id($user_id){
            $user = User::find($user_id);
            return !empty($user->created_by)?$user->created_by:$user->id;
        }

        /* get user_id from token  */
        public static function get_user_id_from_token($token){
            $user = User::where('api_token',$token)->first();
            return $user->id;
        }

        /* get parent_id from token */
        public static function get_parentid_from_token($token){
            $user = User::where('api_token',$token)->first();
            return Self::get_parent_id($user->id);
        }

        /* get admin_id from token */
        public static function get_adminid_from_token($token){
            $user = User::where('api_token',$token)->first();
            if($user != null){
                $role = $user->roles[0]->roles_name;
                if($role == "Super Admin")
                    return $user->id;
                else
                    return $user->created_by;
            }else
                return 0;
        }

        public static function ModuleSectionList(){
            $newArray = [];
            $list = ModuleSection::select('section_slug', 'section_id')->where('parent_section_id', 0)->get();
            if(!empty($list)){
                foreach ($list as $key => $ls) {
                    $newArray[$ls->section_id] = $ls->section_slug; 
                }
            }
            return $newArray;

        }


    /*
        crate custom json reponse
    */
        public static function JSON_RESPONSE($status,$data = array(),$msg){
            return response()->json([
                'status'=>$status,
                'data'=>$data,
                'message'=>$msg
            ],($status)? 200 : 201 );
        }

    /*
        get role name of user
    */
        public static function get_role_from_token($token){
            $user = User::where('api_token',$token)->first();
            // return $user;

            if(isset($user->roles[0]))
                return $user->roles[0]->roles_name;
            else
                return '';
        }

    /*
        function:get_permission_list
    */
        public static function get_permission_list($token){
            $permission_array = [];
            $user = User::where('api_token',$token)->first();
            if(isset($user->roles[0])){

                $section_list = Self::byRoleIdSectionsPermissionList($user->roles[0]->roles_id);
                foreach($section_list as $sec){
                    $permissionIDsd = [];
                    foreach ($sec->permissions as $key => $per) {
                        $permissionIDsd[$per->permissions_ids] = "show";
                    }
                    $permission_array[$sec->section_id] = $permissionIDsd;
                }

            }
            return $permission_array;
        }

        public static function byRoleIdSectionsPermissionList($role_id){
            $role = Role::find($role_id);
            if(!empty($role)){
                $section = $role->sections()->groupBy('section_id')->get();
            // $section = DB::table(config('dbtable.common_role_has_permissions'))->where('roles_id',$role_id)->groupBy('section_id')->get();
                if(!empty($section)){
                    foreach ($section as $key => $sec) {
                        $section[$key]->permissions =  Self::byRoleSectionIdPermission($role_id, $sec->section_id);
                    }
                }
                return $section;
            }else{
                return [];
            }


        }

        public static function byRoleSectionIdPermission($roleid,$sectionid){

            $permissionList = DB::table(config('dbtable.common_role_has_permissions'))->where('roles_id',$roleid)->where('section_id',$sectionid)->get();
            $permission = $permissionList->map(function($per){
                $per->permissions_name = Permission::find($per->permissions_ids)->permissions_name;
                return $per;
            });
            return $permission;

        }

    /*
        check user have permission access or not via user token,page_name
    */
        public static function is_page_access($token, $page, $permission_slug){

            $role_name = Self::get_role_from_token($token);
            
            if($role_name == 'Admin' || $role_name == 'Super Admin'){
                if($role_name == 'Admin' && $permissions_name == 'permission')
                    return false;
                else
                    return true;
            }else{

                $permissionInfo = Permission::where('permissions_slug', $permission_slug)->first();
                $moduleSection = ModuleSection::where('section_slug', $page)->first();
                if($permissionInfo == null || $moduleSection == null )
                    return false;
                
                $permission_list = Self::get_permission_list($token);
                if($permission_list[$moduleSection->section_id][$permissionInfo->permissions_id])
                    return true;
                else
                    return false;

            }
        }

        public static function user_login_history($response){
            return $response;

        }

        public static function essential_config_regenerate($db_id){

            if(isset($db_id) && $db_id != 0){


            //SUBSCRIBER DATABASE
                $db_name = 'support_subs_'.$db_id;


            //USER RELATED TABLE
                $users_table = $db_name.'.usr_users';

                $roles_table = $db_name .'.usr_roles';
                $permissions_table = $db_name . '.usr_permissions';

                $user_has_roles_table = $db_name . '.usr_user_has_roles';
                $role_has_permissions_table = $db_name . '.usr_role_has_permissions';

                $user_logins_table = $db_name . '.usr_user_logins';

            //APP RELATED TABLE
                $notification_table = $db_name .'.app_notification';
                $notification_to_user_table = $db_name .'.app_notification_to_user';
                $settings_type_table = $db_name .'.app_settings';


            //HRM RELATED TABLE
                $staffs_type_table = $db_name .'.hrm_staff';

                $leave_table = $db_name .'.hrm_staff_leave';
                $leave_type_table = $db_name .'.hrm_leave_type';

                $attendance_type_table = $db_name .'.hrm_staff_attendance';
                $designations_type_table = $db_name .'.hrm_designation';
                $departments_type_table = $db_name .'.hrm_department'; 
                $hrm_education = $db_name .'.hrm_education';
                $hrm_document = $db_name .'.hrm_document';
                $hrm_staff_experience = $db_name .'.hrm_staff_experience';
                $hrm_staff_document = $db_name .'.hrm_staff_document';
                $hrm_staff_qualification = $db_name .'.hrm_staff_qualification';
                $hrm_staff_address = $db_name .'.hrm_staff_address';


            //ECOMM RELATED TABLE
                $category_table = $db_name .'.ecm_categories';
                $category_description_table = $db_name .'.ecm_categories_description';

                $products_table = $db_name .'.ecm_products';
                $brand_table = $db_name .'.ecm_brands';

                $usr_role_has_sections = $db_name .'.usr_role_has_sections';
                $usr_role_section_has_permissions = $db_name .'.usr_role_section_has_permissions';
                $mas_translation_key = $db_name .'.app_translation_key';
                $hrm_staff_remark = $db_name .'.hrm_staff_remark';
                $ecm_categories = $db_name .'.ecm_categories';
                $ecm_categories_description = $db_name .'.ecm_categories_description';
                $ecm_products = $db_name .'.ecm_products';
                $ecm_products_description = $db_name .'.ecm_products_description';
                $ecm_products_to_categories = $db_name .'.ecm_products_to_categories';
                $ecm_product_feature = $db_name .'.ecm_product_feature';
                $ecm_product_type_fields = $db_name .'.ecm_product_type_fields';
                $ecm_product_type = $db_name .'.ecm_product_type';
                $ecm_fields = $db_name .'.ecm_fields';
                $ecm_fieldsgroup = $db_name .'.ecm_fieldsgroup';
                $ecm_product_type_to_fieldsgroup = $db_name .'.ecm_product_type_to_fieldsgroup';
                $ecm_products_options = $db_name .'.ecm_products_options';
                $ecm_products_options_values = $db_name .'.ecm_products_options_values';
                $ecm_products_type_field_value = $db_name .'.ecm_products_type_field_value';
                $ecm_products_attributes = $db_name .'.ecm_products_attributes';
                $ecm_images = $db_name .'.ecm_images';
                $ecm_products_to_images = $db_name .'.ecm_products_to_images';
                $ecm_supplier = $db_name .'.ecm_supplier';
                $ecm_seometa = $db_name .'.ecm_seometa';
                $ecm_brand = $db_name .'.ecm_brand';
                $ecm_products_to_supplier = $db_name .'.ecm_products_to_supplier';
                $crm_agent = $db_name .'.crm_agent';
                $crm_industry = $db_name .'.crm_industry';
                $crm_lead = $db_name .'.crm_lead';
                $crm_lead_followup = $db_name .'.crm_lead_followup';
                $crm_lead_source = $db_name .'.crm_lead_source';
                $crm_lead_status = $db_name .'.crm_lead_status';
                $crm_lead_contact = $db_name .'.crm_lead_contact';
                $crm_lead_social_link = $db_name .'.crm_lead_social_link';
                $crm_lead_followup_history = $db_name .'.crm_lead_followup_history';
                $crm_customer = $db_name .'.crm_customer';
                $crm_customer_address = $db_name .'.crm_customer_address';
                $crm_customer_contact = $db_name .'.crm_customer_contact';
                $crm_quotation = $db_name .'.crm_quotation';
                $crm_quotation_item = $db_name .'.crm_quotation_item';
                     
                $crm_setting_payment_terms = $db_name .'.crm_setting_payment_terms';
                $crm_setting_tax = $db_name .'.crm_setting_tax';
                $crm_setting_tax_group = $db_name .'.crm_setting_tax_group';
                $crm_setting_tax_to_tax_group = $db_name .'.crm_setting_tax_to_tax_group';
                $crm_quotation_to_payment_term = $db_name .'.crm_quotation_to_payment_term';
                $ecm_categories_to_options = $db_name .'.ecm_categories_to_options';
               
                



                
                



            //Language
                /*$mas_languages_table='';*/

            } else{

            //SUPER ADMIN DATABASE
                $db_name = 'support_supr';

            //USER RELATED TABLE
                $users_table = $db_name.'.usr_users';
                $roles_table = $db_name .'.usr_roles';
                $permissions_table = $db_name . '.usr_permissions';

                $user_has_roles_table = $db_name . '.usr_user_has_roles';
                $role_has_permissions_table = $db_name . '.usr_role_has_permissions';

                $user_logins_table = $db_name . '.usr_user_logins';


            //APP RELATED TABLE
                $notification_table = $db_name .'.app_notification';
                $notification_to_user_table = $db_name .'.app_notification_to_user';
                $settings_type_table = $db_name .'.app_settings';


            //HRM RELATED TABLE
                $leave_table = $db_name .'.hrm_staff_leave';
                $leave_type_table = $db_name .'.hrm_leave_type';
                $attendance_type_table = $db_name .'.hrm_staff_attendance';
                $hrm_education = $db_name .'.hrm_education';
                $designations_type_table = $db_name .'.hrm_designation';
                $departments_type_table = $db_name .'.hrm_department';
                $hrm_document = $db_name .'.hrm_document';

                $staffs_type_table = $db_name .'.hrm_staff';
                $hrm_staff_experience = $db_name .'.hrm_staff_experience';
                $hrm_staff_document = $db_name .'.hrm_staff_document';
                $hrm_staff_qualification = $db_name .'.hrm_staff_qualification';
                $hrm_staff_address = $db_name .'.hrm_staff_address';
                                

                $usr_role_has_sections = $db_name .'.usr_role_has_sections';
                $usr_role_section_has_permissions = $db_name .'.usr_role_section_has_permissions';
                $mas_translation_key = $db_name .'.app_translation_key';
                $hrm_staff_remark = $db_name .'.hrm_staff_remark';
                $ecm_categories = $db_name .'.ecm_categories';
                $ecm_categories_description = $db_name .'.ecm_categories_description';
                $ecm_products = $db_name .'.ecm_products';
                $ecm_products_description = $db_name .'.ecm_products_description';
                $ecm_products_to_categories = $db_name .'.ecm_products_to_categories';
                $ecm_product_type = $db_name .'.ecm_product_type';
                $ecm_product_feature = $db_name .'.ecm_product_feature';
                $ecm_product_type_fields = $db_name .'.ecm_product_type_fields';
                
                $ecm_fields = $db_name .'.ecm_fields';
                $ecm_fieldsgroup = $db_name .'.ecm_fieldsgroup';
                $ecm_product_type_to_fieldsgroup = $db_name .'.ecm_product_type_to_fieldsgroup';
                $ecm_products_options = $db_name .'.ecm_products_options';
                $ecm_products_options_values = $db_name .'.ecm_products_options_values';
                $ecm_products_type_field_value = $db_name .'.ecm_products_type_field_value';
                
                $ecm_products_attributes = $db_name .'.ecm_products_attributes';
                $ecm_images = $db_name .'.ecm_images';
                $ecm_products_to_images = $db_name .'.ecm_products_to_images';
                $ecm_supplier = $db_name .'.ecm_supplier';
                $ecm_seometa = $db_name .'.ecm_seometa';
                $ecm_brand = $db_name .'.ecm_brand';
                $ecm_products_to_supplier = $db_name .'.ecm_products_to_supplier';
                $crm_agent = $db_name .'.crm_agent';
                $crm_industry = $db_name .'.crm_industry';
                $crm_lead = $db_name .'.crm_lead';
                $crm_lead_followup = $db_name .'.crm_lead_followup';
                $crm_lead_source = $db_name .'.crm_lead_source';
                $crm_lead_status = $db_name .'.crm_lead_status';
                $crm_lead_contact = $db_name .'.crm_lead_contact';

                $crm_lead_social_link = $db_name .'.crm_lead_social_link';
                $crm_lead_followup_history = $db_name .'.crm_lead_followup_history';

                $crm_customer = $db_name .'.crm_customer';
                $crm_customer_address = $db_name .'.crm_customer_address';
                $crm_customer_contact = $db_name .'.crm_customer_contact';
                $crm_quotation = $db_name .'.crm_quotation';
                $crm_quotation_item = $db_name .'.crm_quotation_item';
                
                $crm_setting_payment_terms = $db_name .'.crm_setting_payment_terms';
                $crm_setting_tax = $db_name .'.crm_setting_tax';
                $crm_setting_tax_group = $db_name .'.crm_setting_tax_group';
                $crm_setting_tax_to_tax_group = $db_name .'.crm_setting_tax_to_tax_group';
                $crm_quotation_to_payment_term = $db_name .'.crm_quotation_to_payment_term';
                $ecm_categories_to_options = $db_name .'.ecm_categories_to_options';
                
                


                



            } 

        //Global start
            $db_name2 = 'support_supr';
            $mas_languages_table = $db_name2 .'.mas_languages';
            $mas_translation_table = $db_name2 .'.app_translation';
            $mas_countries_table = $db_name2 .'.mas_countries';
            $mas_currencies_table = $db_name2 .'.mas_currencies';
            $sub_subscription_history_table = $db_name2 .'.sub_subscription_history';
            $sub_subscription_transaction_table = $db_name2 .'.sub_subscription_transaction';
            $sub_plan_table = $db_name2 .'.sub_plan';
            $sub_plan_to_industry_table = $db_name2 .'.sub_plan_to_industry';
            $sub_subscription_table = $db_name2 .'.sub_subscription';
            $mas_countries_timezone_table = $db_name2 .'.mas_countries_timezone';
            $sub_transaction_table = $db_name2 .'.sub_transaction';
            $app_module_table = $db_name2 .'.app_module';
            $app_module_section_table = $db_name2 .'.app_module_section';
            $sub_users_to_business_table = $db_name2 .'.sub_users_to_business';
            $sub_business_user_table = $db_name2 .'.sub_business_user';
            $app_industry_table = $db_name2 .'.app_industry';
            $app_industry_has_module_table = $db_name2 .'.app_industry_has_module';
            $mas_payment_method_table = $db_name2 .'.mas_payment_method';
            $mas_payment_method_details_table = $db_name2 .'.mas_payment_method_details';
            $sub_business_info_table = $db_name2 .'.sub_business_info';
            $sub_business_info_addl_table = $db_name2 .'.sub_business_info_addl';
            $ecm_product_type = $db_name2 .'.ecm_product_type';
            $ecm_fields = $db_name2 .'.ecm_fields';
            $ecm_fieldsgroup = $db_name2 .'.ecm_fieldsgroup';
            $ecm_product_type_to_fieldsgroup = $db_name2 .'.ecm_product_type_to_fieldsgroup';
            // $ecm_products_type_field_value = $db_name2 .'.ecm_products_type_field_value';
                
            
        // Statisc Table with subscriber_demo_db
            $web_settings = 'support_subs_0001.web_settings';
            $web_settings_group = 'support_subs_0001.web_settings_group';
                

                
        //Global end
            config(['dbtable.db_name' => $db_name]);
            config(['dbtable.common_users' => $users_table ]);
            config(['dbtable.common_roles' =>  $roles_table ]);
            config(['dbtable.common_permissions' => $permissions_table ]);
            config(['dbtable.common_user_has_roles' =>  $user_has_roles_table ]);
            config(['dbtable.common_role_has_permissions' =>  $role_has_permissions_table ]);
            config(['dbtable.common_user_logins' =>  $user_logins_table ]);
            config(['dbtable.common_notification' =>  $notification_table ]);
            config(['dbtable.common_notification_to_user' =>  $notification_to_user_table ]);

            config(['dbtable.common_leave' =>  $leave_table ]);
            config(['dbtable.common_leave_type' =>  $leave_type_table ]);
            config(['dbtable.common_attendance' =>  $attendance_type_table ]);
            config(['dbtable.common_setting' =>  $settings_type_table ]);
            config(['dbtable.ecm_images' =>  $ecm_images ]);
            config(['dbtable.ecm_products_to_images' =>  $ecm_products_to_images ]);
            config(['dbtable.ecm_supplier' =>  $ecm_supplier ]);
            config(['dbtable.ecm_seometa' =>  $ecm_seometa ]);
            config(['dbtable.ecm_brand' =>  $ecm_brand ]);
            config(['dbtable.ecm_products_to_supplier' =>  $ecm_products_to_supplier ]);
            
            config(['dbtable.crm_agent' =>  $crm_agent ]);
            config(['dbtable.crm_industry' =>  $crm_industry ]);
            config(['dbtable.crm_lead' =>  $crm_lead ]);
            config(['dbtable.crm_lead_followup' =>  $crm_lead_followup ]);
            config(['dbtable.crm_lead_source' =>  $crm_lead_source ]);
            config(['dbtable.crm_lead_status' =>  $crm_lead_status ]);
            config(['dbtable.crm_lead_contact' =>  $crm_lead_contact ]);
            config(['dbtable.crm_lead_social_link' =>  $crm_lead_social_link ]);
            config(['dbtable.crm_lead_followup_history' =>  $crm_lead_followup_history ]);
            config(['dbtable.ecm_categories_to_options' =>  $ecm_categories_to_options ]);

            config(['dbtable.crm_customer' =>  $crm_customer ]);
            config(['dbtable.crm_customer_address' =>  $crm_customer_address ]);
            config(['dbtable.crm_customer_contact' =>  $crm_customer_contact ]);

            config(['dbtable.crm_quotation' =>  $crm_quotation ]);
            config(['dbtable.crm_quotation_item' =>  $crm_quotation_item ]);

            config(['dbtable.crm_setting_payment_terms' =>  $crm_setting_payment_terms ]);
            config(['dbtable.crm_setting_tax' =>  $crm_setting_tax ]);
            config(['dbtable.crm_setting_tax_group' =>  $crm_setting_tax_group ]);
            config(['dbtable.crm_setting_tax_to_tax_group' =>  $crm_setting_tax_to_tax_group ]);
            config(['dbtable.crm_quotation_to_payment_term' =>  $crm_quotation_to_payment_term ]);

            config(['dbtable.web_settings' =>  $web_settings ]);
            config(['dbtable.web_settings_group' =>  $web_settings_group ]);

            config(['dbtable.common_designations' =>  $designations_type_table ]);
            config(['dbtable.common_departments' =>  $departments_type_table ]);
            config(['dbtable.common_staffs' =>  $staffs_type_table ]);
            config(['dbtable.hrm_education' =>  $hrm_education ]);
            config(['dbtable.hrm_document' =>  $hrm_document ]);
            config(['dbtable.hrm_staff_experience' =>  $hrm_staff_experience ]);
            config(['dbtable.hrm_staff_document' =>  $hrm_staff_document ]);
            config(['dbtable.hrm_staff_qualification' =>  $hrm_staff_qualification ]);
            config(['dbtable.hrm_staff_address' =>  $hrm_staff_address ]);
            config(['dbtable.mas_translation_key' =>  $mas_translation_key ]);
            config(['dbtable.hrm_staff_remark' =>  $hrm_staff_remark ]);
            config(['dbtable.ecm_categories' =>  $ecm_categories ]);
            config(['dbtable.ecm_categories_description' =>  $ecm_categories_description ]);
            config(['dbtable.ecm_products' =>  $ecm_products ]);
            config(['dbtable.ecm_products_description' =>  $ecm_products_description ]);
            config(['dbtable.ecm_products_to_categories' =>  $ecm_products_to_categories ]);
            config(['dbtable.ecm_product_type' =>  $ecm_product_type ]);
            config(['dbtable.ecm_product_feature' =>  $ecm_product_feature ]);
            config(['dbtable.ecm_product_type_fields' =>  $ecm_product_type_fields ]);
            config(['dbtable.ecm_fields' =>  $ecm_fields ]);
            config(['dbtable.ecm_fieldsgroup' =>  $ecm_fieldsgroup ]);
            config(['dbtable.ecm_product_type_to_fieldsgroup' =>  $ecm_product_type_to_fieldsgroup ]);
            config(['dbtable.ecm_products_options' =>  $ecm_products_options ]);
            config(['dbtable.ecm_products_options_values' =>  $ecm_products_options_values ]);
            config(['dbtable.ecm_products_type_field_value' =>  $ecm_products_type_field_value ]);
            config(['dbtable.ecm_products_attributes' =>  $ecm_products_attributes ]);
                
            config(['dbtable.common_mas_languages' =>  $mas_languages_table ]);
            config(['dbtable.common_mas_translation' =>  $mas_translation_table ]);
            config(['dbtable.common_mas_countries' => $mas_countries_table ]);
            config(['dbtable.common_mas_currencies' => $mas_currencies_table ]);
            config(['dbtable.common_sub_subscription_history' => $sub_subscription_history_table ]);
            config(['dbtable.common_sub_subscription_transaction' => $sub_subscription_transaction_table ]);
            config(['dbtable.common_sub_plan' => $sub_plan_table ]);
            config(['dbtable.common_sub_subscription' => $sub_subscription_table ]);
            config(['dbtable.common_mas_countries_timezone' => $mas_countries_timezone_table ]);
            config(['dbtable.common_sub_transaction' => $sub_transaction_table ]);
            config(['dbtable.common_app_module' => $app_module_table ]);
            config(['dbtable.common_app_module_section' => $app_module_section_table ]);
            config(['dbtable.common_sub_users_to_business' => $sub_users_to_business_table ]);
            config(['dbtable.common_sub_business_user' => $sub_business_user_table ]);
            config(['dbtable.common_app_industry' => $app_industry_table ]);
            config(['dbtable.usr_role_has_sections' => $usr_role_has_sections ]);
            config(['dbtable.usr_role_section_has_permissions' => $usr_role_section_has_permissions ]);
            config(['dbtable.common_app_industry_has_module' => $app_industry_has_module_table ]);
            config(['dbtable.common_mas_payment_method' => $mas_payment_method_table ]);
            config(['dbtable.common_mas_payment_method_details' => $mas_payment_method_details_table ]);
            config(['dbtable.common_sub_plan_to_industry' => $sub_plan_to_industry_table ]);
            config(['dbtable.common_sub_business_info' => $sub_business_info_table ]);
            config(['dbtable.common_sub_business_info_addl' => $sub_business_info_addl_table ]);
        }

    /*  store notification here
        {
            param: user_id,type,message,title
            [
                user_id => '',
                type => '',
                message => '',
                title => ''
            ]
        }
    */

        public static function generate_notification($data = array()){

            extract($data);

            $res = NotificationMessage::create([
                "n_type"=>$type,
                "n_title"=>$title,
                "n_message"=>$msg,
                'created_at'=>date("Y-m-d h:s:i")
            ]);

            $response = Notification::create([
                "notification_id"=>$res->id,
                "user_id"=>$user_id,
                "is_read"=>0,
            ]);

            return $res;

        }
/*
    type: numeric,alphabet,alpha_numeric,token
*/

    public static function generate_random_token($type, $size){

        $token = '';

        $alphabet = range("A","Z");
        $numeric = range("1","100");

        switch ($type) {
            case 'numeric':
                shuffle($numeric);
                $res = array_chunk($numeric, $size, true);
                $token = substr(implode('', $res[0]),0,$size);
            break;
            case 'alphabet':
                shuffle($alphabet);
                $res = array_chunk($alphabet, $size, true);
                $token = substr(implode('', $res[0]),0,$size);
            break;
            case 'alpha_numeric':
                $alphabet_num = array_merge($alphabet,$numeric);
                shuffle($alphabet_num);
                $res = array_chunk($alphabet_num, $size, true);
                $token = substr(implode('', $res[0]),0,$size);
            break;
            case 'token':
                $alphabet_num = array_merge($alphabet,$numeric);
                shuffle($alphabet_num);
                $res = array_chunk($alphabet_num, $size, true);
                $token = substr(implode('', $res[0]),0,$size);
            break;

            default:

            break;
        }

        return $token;

    }
    /*

        Get Setting info
        param: DEFAULT_APP_LANGUAGE:OPTONAL
    */
    public static function getSettingInfo($token,$settingKey = ''){


        $res = Setting::select('setting_key','setting_value')->where('user_id',Self::get_adminid_from_token($token));
        
        if(!empty($settingKey))
            $res = $res->where('setting_key',$settingKey)->first();
        else
            $res = $res->get();
        
        return $res;
    } 
    /* 
        get langugae name and translation app_setting wise if admin set default lang to other.
        After Login IF user choose diff language than load transalation list to other lang
    */
    
    public static function getLanguageAndTranslation($token){

        
        $setting = Self::getSettingInfo($token,'APP_DEFAULT_LANGUAGE');
        if(!empty($setting->setting_value)){

            $languages_code = $setting->setting_value;
            $lang_key = "website"; 
            
            $language_data = Language::where('languages_code',$languages_code)->first();
            
            if(!empty($language_data->languages_id))
                $data = Translation::where('languages_id', $language_data->languages_id)
                        ->where('lang_key', $lang_key)->first();
            else
                $data = Translation::where('languages_id', '1')
                        ->where('lang_key', $lang_key)->first();
            
            
            if(!empty($data)){
                $data->lang = $data->language->lang_value;
            }
            return $data;
        }else{
            return '';
        }
    }

    public static function getFullImageUrl($image_ids, $pageType=''){

        $image = '';

        $imageInfo = TempImages::find($image_ids);
        if(!empty($imageInfo)){

            $imageName = $imageInfo->images_name;

            if($pageType == 'index-list')
                $imageName .= '_50x50';
            
            $imageName .='.'.$imageInfo->images_ext;
            $fullImagePath = $imageInfo->images_directory.'/'.$imageName;

            if(Storage::exists($fullImagePath))
                $image = $fullImagePath;
            else
                $image = $imageInfo->images_directory.'/'.$imageInfo->images_name.'.'.$imageInfo->images_ext;

        }
        
        return (!empty($image)) ? Storage::url($image) : url('no-image.png');  
    
    }

    public static function getLangid($lang_code){
        $res = Language::where('languages_code',$lang_code)->first();
        return ($res != null ) ? $res->languages_id : 0;
    } 

    public static function get_subscription_id_by_api_token($token){

        $userid = Self::get_parentid_from_token($token);
        $business = UserBusiness::where('users_id', $userid)->first();
        if($business !== null)
            return !empty($business->subscription) ? $business->subscription->subscription_unique_id : 0;
        else
            return 0 ;

    }




    public static function image_upload_with_crop($api_token,$images_id, $image_type, $file_name, $sub_image_type=''){

        $product_size = ["100x100","250x250","50x50"];
        $gallery_size = ["1000x1000"];

        $size_array = ($sub_image_type == '') ? $product_size : $gallery_size;


        $SubsID= ApiHelper::get_subscription_id_by_api_token($api_token);
        $mainPath = $SubsID;

        if (! Storage::exists($mainPath))
            Storage::makeDirectory($mainPath,0777, true, true);

        $imageInfo = TempImages::find($images_id);

        if(!empty($imageInfo)){
            switch ($image_type) {
                case (1):
                    
                    $folderPath = $mainPath.'/product/'.$file_name;
                    
                    if(!empty($sub_image_type))
                        $folderPath = $folderPath.'/'.$sub_image_type;

                    
                    if (! Storage::exists($folderPath))        // check exist
                        Storage::makeDirectory($folderPath,0777, true, true);

                    // create image
                    $imageName = $imageInfo->images_name.'.'.$imageInfo->images_ext;
                    // $tempImage = Storage::path($imageInfo->images_directory.'/'.$imageName);
                    $tempImage = $imageInfo->images_directory.'/'.$imageName;
                    $liveImage = $folderPath.'/'.$imageName;
                    // return $tempImage;

                    // attach directory to db
                    $imageInfo->images_directory = $folderPath;
                    $imageInfo->images_size = json_encode($size_array);
                    $imageInfo->images_status = 'live';

                    if (Storage::exists($tempImage)) {
                        if(!Storage::exists($liveImage))
                            Storage::move($tempImage, $liveImage);
                    }

                    // ftp://support@143.198.237.54/storage/app/public/temp/1647933995/1647933995.jpg
                   
                   foreach ($size_array as $key => $size) {
                        $exp = explode("x",$size);
                        $width = $exp[0];
                        $height = $exp[1];


                        $image_resize = Image::make(Storage::path($liveImage));
                        $image_resize->resize((int)$width, (int)$height);

                        $saveName = Storage::path($folderPath.'/' .$imageInfo->images_name.'_'.$size.'.'. $imageInfo->images_ext);
                        $image_resize->save($saveName);
                   
                   }

                    

                    break;
                
                default:
                    // code...
                    break;
            }
        }
        $imageInfo->save();

        return $imageInfo;

    }

    public function read_csv_data($fileInfo, $path){

        $file = $fileInfo;
        $path = $file->store($path);
        $file_path = Storage::path($path);
        $file = fopen($file_path,"r");
        $dataList = fgetcsv($file);
        $all_data = [];
        while ( ($data = fgetcsv($file) ) !== FALSE ) {
            array_push($all_data,$data);
        }
        return $all_data;

    }



}
