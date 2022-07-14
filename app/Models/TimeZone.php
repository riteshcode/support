<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeZone extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'timezone_id';
    protected $fillable = [
        'timezone_id',
        'timezone_location',
    ];

    public function getTable()
    {
        return config('dbtable.common_mas_countries_timezone');
    }

}
