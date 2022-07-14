<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ModuleSection;

class Module extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'module_id';
    protected $fillable = [
        'module_id',
        'module_name',
        'module_icon',
        'module_slug',
        'access_priviledge',
        'sort_order',
        'quick_access',
        'status',
        'created_at',
        'updated_at',
    ];

    public function getTable()
    {
        return config('dbtable.common_app_module');
    }

    public function section_list(){
        return $this->hasMany(ModuleSection::class,'module_id','module_id');
    }
}
