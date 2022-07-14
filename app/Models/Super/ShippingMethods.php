<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethods extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'method_id';
    protected $fillable = [
        'method_id',
        'method_name',
        'method_description',
        'method_logo',
        'enabled_country',
        'status',
        'sort_order',
        'created_at',
        'updated_at'
    ];

    public function getTable()
    {
        return config('dbtable.common_mas_shipping_method');
    }

    public function shiping_methods_details(){
        return $this->hasMany(ShippingMethodsDetails::class, 'method_id', 'method_id');
    }

}
