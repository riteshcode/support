<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubscriptionHistory;
use App\Models\PaymentMethodsDetails;
class SubscriptionTransaction extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'subs_txn_id';
    protected $fillable = [
        'subs_txn_id',
        'subscription_id',
        'subs_history_id',
        'invoice_no',
        'payment_amount',
        'transaction_no',
        'payment_method_id',
        'payment_status',
        'payment_details',
        'payment_note',
        'approval_status',
        'approved_at',
        'created_at',
        'updated_at',
    ];
    
    public function getTable()
    {
        return config('dbtable.common_sub_subscription_transaction');
    }
    
    public function subscription_history_details(){
        return $this->belongsTo(SubscriptionHistory::class, 'subs_history_id', 'subs_history_id');
    }
    
    public function payment_method_details(){
        return $this->belongsTo(PaymentMethodsDetails::class, 'payment_method_id', 'payment_method_id');
    }
}
