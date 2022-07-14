<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WebFunctionsSetting;


class WebFunctions extends Model
{
    use HasFactory;

  
    protected $primaryKey = 'function_id';
    protected $guarded = ['function_id'];


      public function getTable()
    {
        return config('dbtable.web_functions');
    }
    
   public function settings_details()
   {

    return $this->hasOne(WebFunctionsSetting::class,'function_id');
   }
 
}
