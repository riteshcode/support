<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlanToIndustry extends Model
{
    use HasFactory;
    protected $primaryKey = 'plan_id';
    protected $fillable = [
        'plan_id',
        'industry_id',
        'status',
        'created_at',
        'updated_at',
    ];
    
    public function getTable()
    {
        return config('dbtable.common_sub_plan_to_industry');
    }

    public function subscription_plan_details(){
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id', 'plan_id');
    }

}

