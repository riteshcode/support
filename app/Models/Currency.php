<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'currencies_id';
    protected $fillable = [
        'currencies_id',
        'currencies_name',
        'currencies_code',
        'symbol_left',
        'symbol_right',
        'decimal_point',
        'thousands_point',
        'decimal_places',
        'value',
        'last_updated',
        'status',
    ];

    public function getTable()
    {
        return config('dbtable.common_mas_currencies');
    }

}
