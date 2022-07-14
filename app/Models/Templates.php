<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TempImages;
use App\Models\TemplatesSectionOptions;
use App\Models\TemplatesSections;

class Templates extends Model
{
    use HasFactory;

    protected $primaryKey='template_id';
    
    public $timestamps = false;
    
     protected $guarded = ['template_id'];

   
   public function image_details()
   {

    return $this->belongsTo(TempImages::class,'images_id','images_id');
   }

      public function sections()
    {
        return $this->hasMany(TemplatesSectionOptions::class,'template_id','template_id');
    }

        
    public function getTable()
    {
        return config('dbtable.web_templates');
    }
    
}
