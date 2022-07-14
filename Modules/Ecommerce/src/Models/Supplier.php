<?php

namespace Modules\Ecommerce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $primaryKey = 'supplier_id';
    
    protected $fillable = [
        'supplier_name',
        'supplier_address',
        'supplier_city',
        'supplier_state',
        'supplier_country',
        'status',
        
       
    ];

    public function getTable(){
        return config('dbtable.ecm_supplier');
    }


}
