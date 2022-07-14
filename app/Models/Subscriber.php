<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'subs_history_id';
    protected $fillable = [
        'business_id',
        'subs_name',
        'subs_email',
        'subs_company_name',
        'subs_contact_no'
    ];

    public function getTable()
    {
        return config('dbtable.common_sub_subscriber_history');
    }

    public function business(){

        return $this->belongsTo(Business::class, 'business_id', 'business_id');

    } 
    public function subscription(){
        return $this->hasMany(Subscription::class, 'subscriber_id', 'subscriber_id');
    }

}
