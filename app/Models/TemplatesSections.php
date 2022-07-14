<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TemplatesSectionOptions;
use App\Models\Templates;


class TemplatesSections extends Model
{
    use HasFactory;

    protected $primaryKey='section_id';
    
    public $timestamps = false;
    
     protected $guarded = ['section_id'];


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
        return config('dbtable.web_template_sections');
    }
    
}
