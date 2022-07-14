<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TempImages;
use App\Models\TemplatesSections;


class TemplatesSectionOptions extends Model
{
    use HasFactory;

    protected $primaryKey='section_option_id';
    
    public $timestamps = false;
    
   protected $guarded = ['section_option_id'];

    public function getTable()
    {
        return config('dbtable.web_template_section_options');
    }
    
     public function image_details()
   {

    return $this->belongsTo(TempImages::class,'images_id','images_id');

   } 

   

   
}
