<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowingRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrowing_request_id',
        'book_id',
    ];

    public function borrowingRequest()
    {
        return $this->belongsTo(BorrowingRequest::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}