<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TemplateComponent;


class ComponentSetting extends Model
{
    use HasFactory;

    protected $primaryKey='setting_id';
    
    public $timestamps = false;
    
    protected $fillable = ['template_id',
    
    'component_id',
    'sort_order',
            
    ];

   
        
    public function getTable()
    {
        return config('dbtable.web_template_component_setting');
    }


    public function component_details()
    {
        return $this->belongsTo(TemplateComponent::class,'component_id','component_id');
    }
    
}
