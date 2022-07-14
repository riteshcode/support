<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteSettingsGroup extends Model
{
    use HasFactory;

    // protected $table = "website_setting";
    protected $primaryKey = 'group_id';
    protected $fillable = [
            'group_name',
            'status',
            'created_at',
            'updated_at',
    ];

    public function getTable() 
    {
        return config('dbtable.web_settings_group');
    }

    public function settings(){
        return $this->hasMany(WebsiteSetting::class, 'group_id','group_id');
    }
}
