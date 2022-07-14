<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ComponentSetting;

class TemplateComponent extends Model
{
    use HasFactory;

    protected $primaryKey='component_id';
    
    public $timestamps = false;
    
    protected $fillable = ['component_name',
    
    'component_key',
    'parent_id',
    'sort_order',
            
    ];

   
        
    public function getTable()
    {
        return config('dbtable.web_template_component');
    }
    
    public function component()
    {
        return $this->hasMany(ComponentSetting::class,'component_id');
    }
}
