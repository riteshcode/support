<?php

namespace Modules\Ecommerce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Ecommerce\Models\CategoryDescription;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = "categories_id";

    protected $fillable = [
        'categories_slug',
        'categories_image',
        'categories_icon',
        'parent_id',
        'sort_order',
        'status'
    ];

    public function getTable(){
        return config('dbtable.ecm_categories');
    }

    public function categorydescription(){
        return $this->hasMany(CategoryDescription::class, 'categories_id', 'categories_id');
    }
}
