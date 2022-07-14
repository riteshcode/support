<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuGroup extends Model
{
    use HasFactory;

    protected $primaryKey = "group_id";

    public $timestamps = false;

    protected $guarded=[
     'group_id',
    ];
    
    public function getTable()
    {
        return config('dbtable.web_menu_group');
    }

   



}
