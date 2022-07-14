<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethods extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'payment_method_id';
    protected $fillable = [
        'payment_method_id',
        'method_name',
        'method_description',
        'method_logo',
        'status',
        'sort_order',
        'created_at',
        'updated_at'
    ];

    public function getTable()
    {
        return config('dbtable.common_mas_payment_method');
    }

    public function payment_methods_details(){
        return $this->hasMany(PaymentMethodsDetails::class, 'payment_method_id', 'payment_method_id');
    }

}
