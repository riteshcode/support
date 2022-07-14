<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessInfoAdditional extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'business_id';
    protected $fillable = [
        'business_id',
        'notify_email',
        'secondary_phone',
        'whatsapp_no',
        'status',
        'created_at',
        'updated_at',
    ];
    
    public function getTable()
    {
        return config('dbtable.common_sub_business_info_addl');
    }
}
