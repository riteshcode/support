<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;
    protected $table = "payment_setting";

    protected $fillable = [
        'paytm_status',
        'paytm_mode',
        'paytm_merchant_key',
        'paytm_secret_key',
        'created_by'
    ];
}
