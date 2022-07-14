<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BannerGroup;

class Banner extends Model
{
    use HasFactory;

    protected $primaryKey = "banners_id";

    public $timestamps = false;

    protected $guarded=[
     
     'banners_id',


    ];
     
    
    public function getTable()
    {
        return config('dbtable.web_banners');
    }

    public function bannerGroup(){
        return $this->belongsTo(BannerGroup::class, 'banners_group_id','banners_group_id');
    }



}
