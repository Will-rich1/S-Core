<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke User
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Relasi ke Category (PENTING)
    public function category()
    {
        // Sesuaikan dengan nama kolom di database (biasanya student_category_id)
        return $this->belongsTo(Category::class, 'student_category_id');
    }

    // Relasi ke Subcategory (PENTING)
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'student_subcategory_id');
    }
}