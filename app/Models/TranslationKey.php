<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslationKey extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = "translation_key_id";
    
    protected $fillable = [
        'section_id',
        'translation_key',
        'source',
    ];

    public function getTable()
    {
        return config('dbtable.mas_translation_key');
    }

}

