<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebFunctionsIndustry extends Model
{
    use HasFactory;

  
   // protected $primaryKey = 'industry_id';
    protected $fillable = ['industry_id','function_id'];


      public function getTable()
    {
        return config('dbtable.web_functions_to_industry');
    }
    


}
