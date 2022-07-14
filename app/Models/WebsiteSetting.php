<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    use HasFactory;

   // protected $table = "website_setting";
   protected $primaryKey = 'setting_id';
    protected $fillable = [
            'group_id',
            'setting_key',
            'setting_name',
            'setting_value',
            'status',
            'created_at',
            'updated_at',
    ];

    public function getTable() 
    {
        return config('dbtable.web_settings');
    }
}
