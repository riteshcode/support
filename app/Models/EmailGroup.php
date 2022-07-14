<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class EmailGroup extends Model
{
    use HasFactory;

    protected $primaryKey = "group_id";

    public $timestamps = false;

    protected $guarded=[
     'group_id',
    ];
     
    
    public function getTable()
    {
        return config('dbtable.app_email_group');
    }

    


}
