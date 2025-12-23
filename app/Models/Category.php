<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_mandatory', 'display_order', 'is_active', 'created_by'];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    // --- TAMBAHKAN INI AGAR BISA HAPUS ---
    public function submissions()
    {
        // Pastikan 'student_category_id' sesuai dengan nama kolom di database Anda
        return $this->hasMany(Submission::class, 'student_category_id');
    }
}