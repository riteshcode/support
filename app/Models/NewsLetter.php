<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class NewsLetter extends Model
{
    use HasFactory;

    protected $primaryKey = "newsletter_id";

    public $timestamps = false;

    protected $guarded=[
     
     'newsletter_id',


    ];
     
    
    public function getTable()
    {
        return config('dbtable.web_newsletter');
    }

    

}
