<?php

namespace Modules\Ecommerce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsOptionsValues extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'products_options_values_id';
    

    protected $fillable = [
       'products_options_id',
       'products_options_values_name',
    ];
    public $timestamps=false;

    public function getTable(){
        return config('dbtable.ecm_products_options_values');
    }


}
