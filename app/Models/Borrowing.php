<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'borrowed_date',
        'due_date',
        'return_date',
        'status',
    ];

    protected $casts = [
        'borrowed_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function isOverdue()
    {
        return $this->status === 'active' && Carbon::now()->greaterThan($this->due_date);
    }

    public function isDueSoon()
    {
        $daysUntilDue = Carbon::now()->diffInDays($this->due_date, false);
        return $this->status === 'active' && $daysUntilDue >= 0 && $daysUntilDue <= 3;
    }
}