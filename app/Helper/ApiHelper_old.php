<?php
namespace App\Helper;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Notification;
use App\Models\NotificationMessage;

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
        $user = User::with("roles")->where('api_token',$token)->first();
        if($user != null){
            $role = $user->roles[0]->name;
            if($role == "superadmin" || $role == "admin")
                return $user->id;
            else
                return $user->created_by;
        }else
            return 0;
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
        if(isset($user->roles[0]))
            return $user->roles[0]->display_name;
        else
            return '';
    }

    /*
        function:get_permission_list
    */
    public static function get_permission_list($token){
        $permission_array = [];
        $user = User::where('api_token',$token)->first();
        foreach ($user->roles[0]->permissions as $key => $permission)
            $permission_array[$key] = $permission->name;

        return $permission_array;
    }

    /*
        check user have permission access or not via user token,page_name
    */
    public static function is_page_access($token, $permission_name){
        $role_name = Self::get_role_from_token($token);
        if($role_name == 'admin' || $role_name == 'superadmin'){
            if($role_name == 'admin' && $permission_name == 'permission')
                return false;
            else
                return true;
        }else{
            $permission_list = Self::get_permission_list($token);
            if(in_array($permission_name, $permission_list))
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
            $db_name = 'invoidea_support'.$db_id;
            $users_table = $db_name.'.users';
            $roles_table = $db_name .'.roles';
            $permissions_table = $db_name . '.permissions';
            $common_role_has_permissions = $db_name . '.role_has_permissions';
            $user_has_roles_table = $db_name . '.user_has_roles';
            $model_has_permissions_table = $db_name . '.model_has_permissions';

        } else{
            $db_name = 'invoidea_support';
            $users_table = $db_name.'.sa_users';
            $roles_table = $db_name .'.sa_roles';
            $permissions_table = $db_name . '.sa_permissions';
            $common_role_has_permissions = $db_name . '.sa_role_has_permissions';
            $user_has_roles_table = $db_name . '.sa_user_has_roles';
            $model_has_permissions_table = $db_name . '.sa_model_has_permissions';

        }


        config(['dbtable.db_name' => $db_name]);

        config(['dbtable.common_users' => $users_table ]);
        config(['dbtable.common_roles' =>  $roles_table ]);
        config(['permission.table_names.roles' =>  $roles_table ]);

        config(['dbtable.common_permissions' => $permissions_table ]);
        config(['permission.table_names.permissions' => $permissions_table ]);

        // config(['dbtable.common_role_has_permissions' => $role_has_permissions_table ]);
        config(['permission.table_names.common_role_has_permissions' => $common_role_has_permissions ]);

        config(['dbtable.common_user_has_roles' => $user_has_roles_table ]);
        config(['permission.table_names.model_has_roles' => $model_has_roles_table ]);

        config(['dbtable.common_model_has_permissions' => $model_has_permissions_table ]);
        config(['permission.table_names.model_has_permissions' => $model_has_permissions_table ]);

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



}
