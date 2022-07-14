<?php

namespace Modules\Ecommerce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryDescription extends Model
{
    use HasFactory;

    protected $primaryKey = "categories_description_id";

    protected $fillable = [
        'categories_id',
        'languages_id',
        'categories_name',
        'categories_description',
        'categories_title',
        'categories_meta_desc',
    ];
    public $timestamps = false;

    public function getTable(){
        return config('dbtable.ecm_categories_description');
    }

    public function category(){
        $this->belongsTo(Category::class,'categories_id','categories_id');
    }

}
