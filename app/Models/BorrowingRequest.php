<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BorrowingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'qr_code',
        'pickup_date',
        'return_date',
        'status',
        'expires_at',
        'verified_at',
        'returned_at',
    ];

    protected $casts = [
        'pickup_date'  => 'date',
        'return_date'  => 'date',
        'expires_at'   => 'datetime',
        'verified_at'  => 'datetime',
        'returned_at'  => 'datetime',
    ];

    // ── Relasi ────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(BorrowingRequestItem::class);
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'borrowing_request_items');
    }

    // ── Helper ────────────────────────────────────────────────
    public function isExpired(): bool
    {
        return $this->status === 'pending'
            && $this->expires_at
            && Carbon::now()->isAfter($this->expires_at);
    }

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isActive(): bool    { return $this->status === 'active'; }
    public function isReturned(): bool  { return $this->status === 'returned'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }

    /** Label status bahasa Indonesia */
    public function statusLabel(): string
    {
        return match($this->status) {
            'pending'   => 'Menunggu Verifikasi',
            'active'    => 'Sedang Dipinjam',
            'returned'  => 'Sudah Dikembalikan',
            'cancelled' => 'Dibatalkan',
            default     => ucfirst($this->status),
        };
    }

    /** Badge CSS class */
    public function statusClass(): string
    {
        return match($this->status) {
            'pending'   => 'badge-pending',
            'active'    => 'badge-active',
            'returned'  => 'badge-returned',
            'cancelled' => 'badge-cancelled',
            default     => '',
        };
    }

    /** Generate kode QR unik DIGI-XXXXXX */
    public static function generateQrCode(): string
    {
        do {
            $code = 'DIGI-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } while (self::where('qr_code', $code)->exists());

        return $code;
    }
}