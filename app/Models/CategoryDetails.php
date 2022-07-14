<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryDetails extends Model
{
    use HasFactory;
    protected $table = "categories_details";

    protected $fillable = [
        'category_name',
        'description',
        'categories_id',
        'languages_id',
    ];

    public function category(){
        $this->belongsTo(Category::class);
    }

}
