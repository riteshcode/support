<?php


return [
    'db_id' => '',
    //'db_name' => 'invoidea_support'.config('dbtable.db_id'),

    //COMMON UNIVERSAL TABLE
    'countries' => 'invoidea_support.ms_countries',
    'currencies' => 'invoidea_support.ms_currencies',
    'languages' => 'invoidea_support.ms_languages',


    //'common_users' => config('dbtable.db_name') . (config('dbtable.db_name') == 'invoidea_support')? 'sa_users' : 'users',
    //'common_roles' =>  config('dbtable.db_name').'sa_roles',
    //'common_permissions' => config('dbtable.db_name').'sa_permissions',
    //'common_users' => config('dbtable.db_name').'sa_users',
    //'common_roles' => config('dbtable.db_name').'sa_roles',
    //'common_permissions' => config('dbtable.db_name').'sa_permissions',
    //'common_role_has_permissions' => config('dbtable.db_name').'sa_role_has_permissions',
   // 'common_model_has_roles' => config('dbtable.db_name').'sa_model_has_roles',
   // 'common_model_has_permissions' => config('dbtable.db_name').'sa_model_has_permissions',

    'super_users' => 'invoidea_support.sa_users',
    'admin_users' => 'invoidea_support1.users',


];
