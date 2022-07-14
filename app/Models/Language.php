<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;
    /*protected $table = "mas_languages";*/

    protected $primaryKey = "languages_id";

    public $timestamps = false;

    protected $fillable = [
        'languages_id', 'languages_name', 'languages_code', 'languages_icon', 'text_align', 'is_default', 'date_added', 'last_modified', 'status',
    ];

    public function getTable()
    {
        return config('dbtable.common_mas_languages','mas_languages');
    }

    public function translation(){
        return $this->hasMany(Translation::class, 'languages_id', 'languages_id');
    }
}
