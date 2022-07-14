<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'business_id',
        'business_name',
        'business_description',
        'country_id',
        'tax_info',
    ];
    
    public function getTable()
    {
        return config('dbtable.common_sub_business');
    }
}
