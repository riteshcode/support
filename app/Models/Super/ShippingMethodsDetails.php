<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethodsDetails extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'payment_method_id';
    protected $fillable = [
        'method_key_id',
        'method_id',
        'method_key',
        'status',
        'created_at',
        'updated_at'
    ];

    public function getTable()
    {
        return config('dbtable.common_mas_shipping_method_key');
    }

}
