<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TemplatesSection;
use App\Models\Templates;


class TemplatesSectionGroup extends Model
{
    use HasFactory;

    protected $primaryKey='group_id';
    
    public $timestamps = false;
    
     protected $guarded = ['group_id'];


    public function section_list()
    {
        return $this->hasMany(TemplatesSection::class,'group_id','group_id');
    }
     
   
    public function templates()
    {
        return $this->belongsTo(Templates::class,'template_id','template_id');
    }


    public function getTable()
    {
        return config('dbtable.web_template_section_group');
    }
    
}
