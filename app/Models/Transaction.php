<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'invoice_no',
        'tax_amount',
        'discount_type',
        'final_amount',
        'transaction_date',
        'payment_method',
        'payment_status',
        'payment_details',
        'note',
        'date_added'
    ];
    
    public function getTable()
    {
        return config('dbtable.common_sub_transaction');
    } 

    public function payment(){
        return $this->belongsTo(PaymentMethods::class, 'payment_method', 'payment_methods_id');
    }
}
