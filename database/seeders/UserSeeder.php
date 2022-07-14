<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	
    	$role = Role::create(['name' => 'superadmin','display_name'=>'superadmin']);
    	    	
        $user = User::create([
            'name' => Str::random(10),
            'email' => 'superadmin@invoidea.com',
            'password' => Hash::make('password'),
            'api_token' => Hash::make(Str::random(10)),
        ]);
        $user->assignRole('superadmin');

        $role = Role::create(['name' => 'admin','display_name'=>'admin','created_by'=>$user->id]);   
        $user = User::create([
            'name' => Str::random(10),
            'email' => 'admin@invoidea.com',
            'password' => Hash::make('password'),
            'api_token' => Hash::make(Str::random(10)),
            'created_by'=>$user->id,
        ]);
        $user->assignRole('admin');

        // creating permission
        $permission_list = ['user.view','user.create','user.edit','user.destroy','role.view','role.create','role.edit','role.destroy'];
        foreach ($permission_list as $key => $value) {
            Permission::create(['name'=>$value]);    
        }
        

    }
}
