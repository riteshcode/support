<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TempImages;
use App\Models\TemplatesSectionGroup;

class TemplatesSetting extends Model
{
    use HasFactory;

    protected $primaryKey='template_setting_id';
    
    public $timestamps = false;
    
     protected $guarded = ['template_setting_id'];

   
        
    public function getTable()
    {
        return config('dbtable.web_template_setting');
    }
    
}
