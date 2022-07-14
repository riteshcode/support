<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WebPagesDescription;

class WebPages extends Model
{
    use HasFactory;

   // protected $table = "website_setting";
   protected $primaryKey = 'pages_id';
   protected $guarded = ['pages_id'];


    public function getTable() 
    {
        return config('dbtable.web_pages');
    }


    public function pagedescription(){
        return $this->hasMany(WebPagesDescription::class, 'pages_id', 'pages_id');
    }

}
