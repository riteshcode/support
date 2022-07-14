<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $primaryKey = 'setting_id';
    protected $fillable = [
        'user_id',
        'setting_key',
        'setting_value'
    ];

    public function getTable(){
        return config('dbtable.common_setting');
    }

}
