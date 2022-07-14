<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    use HasFactory;
    //protected $table='ecm_images';
    protected $primaryKey='images_id';
    public $timestamps = false;
    protected $fillable=
    [
        'images_type',
        'images_name',
        'images_ext',
        'images_directory',
        'images_size',
        
    ];
    public function getTable()
    {
        return config('dbtable.ecm_images');
    }
    
}
