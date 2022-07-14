<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class SubsComponentSetting extends Model
{
    use HasFactory;

    protected $primaryKey='setting_id';
    
    public $timestamps = false;
    
    protected $guarded = ['setting_id',       
    ];

   
        
    public function getTable()
    {
        return config('dbtable.subscriber_web_template_component_setting');
    }


    
}
