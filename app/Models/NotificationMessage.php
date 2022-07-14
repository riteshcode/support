<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationMessage extends Model
{
    use HasFactory;

    // protected $table = 'su_notification';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'n_type',
        'n_title',
        'n_message',
        'created_at',
    ];
    public $timestamps = false;


    public function getTable()
    {
        return config('dbtable.common_notification');
    }

}



