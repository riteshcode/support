<?php
namespace App\Helper;
use Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Helper
{
	/*
		funcation:show_role_name()
		// In future need to add something we can directly add here 
		so every place will change.
	*/

	public static function show_role_name(){
		return Auth::user()->roles[0]->name;
	}

	/*
		function : get_parent_id()
		this function is used to get parent id. so we don't need to check everytime created by empty	
	*/
	public static function get_parent_id(){
		if(Self::show_role_name() == 'admin' || Self::show_role_name() == 'superadmin')
			return Auth::id();
		else
			return Auth::user()->created_by;

	}

	/*
		function:get_permission_list
	*/

	public static function get_permission_list(){
		$permission_array = [];
		foreach (Auth::user()->roles[0]->permissions as $key => $permission)
			$permission_array[$key] = $permission->name;  			

		return $permission_array;
	}

	/*
		check user have permission access or not for controller 
	*/

	public static function is_user_access($permission_name){
		if(Self::show_role_name() == 'admin' || Self::show_role_name() == 'superadmin'){
			if(Self::show_role_name() == 'admin' && $permission_name == 'permission')
				return false;
			else
				return true;

		}else{
			$permission_list = Self::get_permission_list();
			if(in_array($permission_name, $permission_list))
				return true;
			else
				return false;
		}
	}


	/*
		check user have permission access or not for navbar hide and show 
	*/

	public static function is_user_access_nav($permission_name){
		if(Self::show_role_name() == 'admin')
			return true;
		else{
			$permission_list = Self::get_permission_list();
			if(in_array($permission_name, $permission_list))
				return true;
			else
				return false;
		}
	}






}



