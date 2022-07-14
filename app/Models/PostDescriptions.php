<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PostDescriptions extends Model
{
    use HasFactory;

    protected $primaryKey = "post_description";

    public $timestamps = false;

    protected $guarded=[
     
     'post_description',


    ];
     
    
    public function getTable()
    {
        return config('dbtable.web_post_descriptions');

    }

    

}