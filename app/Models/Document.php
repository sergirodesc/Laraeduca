<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_type_id',
        'expiry_date',
        'file_path',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}