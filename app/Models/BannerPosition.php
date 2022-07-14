<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerPosition extends Model
{
    use HasFactory;

    protected $primaryKey = "banner_position_id";

    public $timestamps = false;

    protected $fillable = [
        'position_key',
        'position_name',
    ];
    
    public function getTable()
    {
        return config('dbtable.web_banner_position');
    }
}
