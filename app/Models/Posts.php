<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PostDescriptions;


class Posts extends Model
{
    use HasFactory;

    protected $primaryKey = "post_id";

    public $timestamps = false;

    protected $guarded=[
     
     'post_id',
    ];
     
    
    public function getTable()
    {
        return config('dbtable.web_posts');
    }

    public function descriptionDetails(){
        return $this->hasMany(PostDescriptions::class, 'post_id', 'post_id');
    }


}
