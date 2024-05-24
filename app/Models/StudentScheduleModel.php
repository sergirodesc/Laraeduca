<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentScheduleModel extends Model
{
    use HasFactory;

    protected $table = 'student_schedules';

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'admin_approval',
        'employee_approval',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'admin_approval' => 'boolean',
        'employee_approval' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
