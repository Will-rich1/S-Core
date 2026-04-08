<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPointResetHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'admin_id',
        'total_points_before',
        'total_points_after',
        'affected_submissions',
        'snapshot',
    ];

    protected function casts(): array
    {
        return [
            'snapshot' => 'array',
            'total_points_before' => 'decimal:2',
            'total_points_after' => 'decimal:2',
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
