<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BannerPosition;

class BannerSetting extends Model
{
    use HasFactory;

    protected $primaryKey = "banner_setting_id";

    public $timestamps = false;

    protected $fillable = [
        'template_id',
        'refrence_id',
        'banner_position_id',
        'images_id',
        'comment',
    ];
    
    public function getTable()
    {
        return config('dbtable.web_banner_setting');
    }

    public function position(){
        return $this->belongsTo(BannerPosition::class, 'banner_position_id','banner_position_id');
    }



}
