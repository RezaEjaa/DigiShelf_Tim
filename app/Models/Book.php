<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'description',
        'publisher',
        'publication_year',
        'stock',
        'available',
        'cover_image',
    ];

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function isAvailable()
    {
        return $this->available > 0;
    }
}