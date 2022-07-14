<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;
    public $timestamps = false;
    
    protected $primaryKey = "translation_id";

    protected $fillable = [
        'translation_id',
        'languages_id',
        'lang_key',
        'lang_value'
    ];

    public function getTable()
    {
        return config('dbtable.common_mas_translation','app_translation');
    }

    public function language(){
        return $this->belongsTo(Language::class, 'languages_id', 'languages_id');
    }
}

