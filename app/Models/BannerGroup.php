<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerGroup extends Model
{
    use HasFactory;

    protected $primaryKey = "banners_group_id";

    public $timestamps = false;

    protected $guarded=[
     
     'banners_group_id',


    ];
    
    public function getTable()
    {
        return config('dbtable.web_banners_group');
    }

   



}
