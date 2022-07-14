<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    use HasFactory;

  //  protected $table = 'web_order_items';
    protected $primaryKey = 'order_item_id';
    protected $guarded = ['order_item_id'];


      public function getTable()
    {
        return config('dbtable.web_order_items');
    }
    


}
