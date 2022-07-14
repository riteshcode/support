<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BusinessInfo;
use App\Models\BusinessInfoAdditional;

class BusinessUser extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'sub_business_user';
    protected $fillable = [
        'business_id',
        'business_unique_id',
        'business_name',
        'business_email',
        'business_icon',
        'status',
        'signup_at',
        'created_at',
        'updated_at',
    ];

    public function business_info(){
        return $this->hasOne(BusinessInfo::class, 'business_id', 'business_id');
    }

    public function business_info_addl(){
        return $this->hasOne(BusinessInfoAdditional::class, 'business_id', 'business_id');
    }
}
