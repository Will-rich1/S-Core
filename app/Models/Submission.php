<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $guarded = ['id']; // Semua kolom boleh diisi kecuali ID

    // Relasi ke User (Mahasiswa)
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Relasi ke Kategori yang dipilih Mahasiswa
    public function category()
    {
        return $this->belongsTo(Category::class, 'student_category_id');
    }

    // Relasi ke Subkategori yang dipilih Mahasiswa
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'student_subcategory_id');
    }
}