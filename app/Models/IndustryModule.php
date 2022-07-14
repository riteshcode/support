<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndustryModule extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'industry_id';
    protected $fillable = [
        'industry_id',
        'module_id',
        'sort_order',
        'status',
        'created_at',
        'updated_at',
    ];

    public function getTable()
    {
        return config('dbtable.common_app_industry_has_module');
    }
}
