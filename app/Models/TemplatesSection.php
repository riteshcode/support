<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TempImages;
use App\Models\TemplatesSectionGroup;


class TemplatesSection extends Model
{
    use HasFactory;

    protected $primaryKey='section_id';
    
    public $timestamps = false;
    
   protected $guarded = ['section_id'];

    public function getTable()
    {
        return config('dbtable.web_template_sections');
    }
    
     public function image_details()
   {

    return $this->belongsTo(TempImages::class,'images_id','images_id');

   } 

   public function section_group()
   {

    return $this->belongsTo(TemplatesSectionGroup::class,'group_id','group_id');
   }

   
}
