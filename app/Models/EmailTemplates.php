<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class EmailTemplates extends Model
{
    use HasFactory;

    protected $primaryKey = "template_id";

    public $timestamps = false;

    protected $guarded=[
     'template_id',
    ];
     
    public function getTable()
    {
        return config('dbtable.app_email_templates');
    }

    


}
