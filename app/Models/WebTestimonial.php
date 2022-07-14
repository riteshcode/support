<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebTestimonial extends Model
{
    use HasFactory;

  
    protected $primaryKey = 'testimonial_id';
    protected $fillable = ['customer_id','testimonial_title','testimonial_text','status'];
    public $timestamps=false;


      public function getTable()
    {
        return config('dbtable.web_testimonial');
    }
    


}
