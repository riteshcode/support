<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Country;

class BusinessInfo extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'business_id';
    protected $fillable = [
        'business_id',
        'billing_contact_name',
        'billing_email',
        'billing_street_address',
        'billing_city',
        'billing_state',
        'billing_country',
        'billing_zipcode',
        'billing_phone',
        'billing_gst',
        'billing_default',
        'status',
        'created_at',
        'updated_at',
    ];
    
    public function getTable()
    {
        return config('dbtable.common_sub_business_info');
    }

    public function country(){
        return $this->belongsTo(Country::class, 'billing_country', 'countries_id');
    }
}
