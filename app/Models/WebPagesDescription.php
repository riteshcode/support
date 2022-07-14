<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebPagesDescription extends Model
{
    use HasFactory;

   // protected $table = "website_setting";
   protected $primaryKey = 'page_description_id';
   protected $guarded = ['page_description_id'];

    public function getTable() 
    {
        return config('dbtable.web_pages_description');
    }
}
