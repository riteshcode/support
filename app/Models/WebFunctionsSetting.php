<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebFunctionsSetting extends Model
{
    use HasFactory;

  
    protected $primaryKey = 'function_id';
    protected $fillable = ['function_id','status'];
    public $timestamps=false;


      public function getTable()
    {
        return config('dbtable.web_function_settings');
    }
    


}
