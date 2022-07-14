<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MenuGroup;

class Menu extends Model
{
    use HasFactory;

    protected $primaryKey = "menu_id";

    protected $guarded=[
     
     'menu_id',
    ];
     
    
    public function getTable()
    {
        return config('dbtable.web_menu');
    }

    public function bannerGroup(){
        return $this->belongsTo(MenuGroup::class, 'group_id','group_id');
    }



}
