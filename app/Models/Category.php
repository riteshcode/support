<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "categories";

    protected $fillable = [
        'categories_image',
        'categories_icon',
        'business_id',
        'parent_id',
        'is_featured',
        'sort_order',
        'created_by',
        'categories_slug',
        'categories_status'
    ];

    public function categorydetails(){
        return $this->hasMany(CategoryDetails::class, 'categories_id', 'id');
    }
}
