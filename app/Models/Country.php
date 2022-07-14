<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Currency;
use App\Models\TimeZone;
use App\Models\Language;
class Country extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "ms_countries";
    protected $primaryKey = 'countries_id';
    protected $fillable = [
        'countries_id',
        'countries_name',
        'countries_iso_code_2',
        'countries_iso_code_3',
        'currencies_id',
        'currencies_code',
        'languages_id',
        'time_zone_id',
        'utc_time',
        'address_format_id',
    ];

    public function getTable()
    {
        return config('dbtable.common_mas_countries');
    }

    public function currency(){
        return $this->belongsTo(Currency::class, 'currencies_id', 'currencies_id');
    }
    
    public function timezone(){
        return $this->belongsTo(TimeZone::class, 'time_zone_id', 'timezone_id');
    }

    public function language(){
        return $this->belongsTo(Language::class, 'languages_id', 'languages_id');
    }
}
