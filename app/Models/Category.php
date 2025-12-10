<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_mandatory', 'display_order', 'is_active', 'created_by'];

    // INI YANG HILANG TADI: Memberi tahu bahwa 1 Kategori punya BANYAK Subkategori
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
}