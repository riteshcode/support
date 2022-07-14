<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Industry;

class SubscriptionPlan extends Model
{
    use HasFactory;
    protected $primaryKey = 'plan_id';
    protected $fillable = [
        'plan_id',
        'plan_name',
        'plan_desc',
        'plan_amount',
        'plan_discount',
        'discount_type',
        'plan_duration',
        'featured',
        'status',
        'created_at',
        'updated_at',
    ];
    
    public function getTable()
    {
        return config('dbtable.common_sub_plan');
    }

    public function plan_to_industry(){
        return $this->belongsToMany(Industry::class, config('dbtable.common_sub_plan_to_industry'),'plan_id','industry_id');
    }

}

