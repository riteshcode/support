<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\IndustryModule;
use App\Models\Module;
class Industry extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'industry_id';
    protected $fillable = [
        'industry_id',
        'industry_name',
        'sort_order',
        'industry_type',
        'status',
        'created_at',
        'updated_at',
    ];

    public function getTable() 
    {
        return config('dbtable.common_app_industry');
    }

    public function modules(){
        return $this->belongsToMany(Module::class, config('dbtable.common_app_industry_has_module'),'industry_id','module_id');
    }
    public function web_functions(){
        return $this->belongsToMany(WebFunctions::class, config('dbtable.web_functions_to_industry'),'industry_id','function_id');
    }
}
