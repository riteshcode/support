<?php

namespace Modules\Ecommerce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'products_attributes_id';

    protected $fillable = [
        'products_id',
        'options_id',
        'options_values_id',
        'options_values_price',
        'is_default',
    ];

    public $timestamps = false;

    public function getTable(){
        return config('dbtable.ecm_products_attributes');
    }


}
