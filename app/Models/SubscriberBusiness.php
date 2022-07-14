<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BusinessInfo;
use App\Models\BusinessInfoAdditional;
use App\Models\Subscription;

class SubscriberBusiness extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'business_id';
    protected $fillable = [
        'business_id',
        'business_unique_id',
        'business_name',
        'business_icon',
        'status',
        'signup_at',
        'created_at',
        'updated_at',
    ];
    
    public function getTable()
    {
        return config('dbtable.common_sub_business_user');
    }

    public function business_info(){
        return $this->hasOne(BusinessInfo::class, 'business_id', 'business_id');
    }

    public function business_info_additional(){
        return $this->hasOne(BusinessInfoAdditional::class, 'business_id', 'business_id');
    }

    public function subscription(){
        return $this->hasMany(Subscription::class, 'business_id', 'business_id');
    }
}
