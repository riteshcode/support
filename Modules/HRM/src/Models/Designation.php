<?php

namespace Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Designation extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'designation_id',
        'designation',
        'created_by',
        'created_at',
        'updated_at',
    ]; 


    public function getTable(){
        return config('dbtable.common_designations');
    }

    public function user(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

}
