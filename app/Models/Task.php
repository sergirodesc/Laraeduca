<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'team_id'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
