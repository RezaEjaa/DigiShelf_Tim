<?php

namespace App\Console\Commands;

use App\Models\BorrowingRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CancelExpiredBorrowings extends Command
{
    protected $signature   = 'borrowings:cancel-expired';
    protected $description = 'Batalkan otomatis peminjaman pending yang sudah lebih dari 24 jam';

    public function handle(): int
    {
        $count = BorrowingRequest::where('status', 'pending')
            ->where('expires_at', '<', Carbon::now())
            ->update(['status' => 'cancelled']);

        $this->info("Berhasil membatalkan {$count} peminjaman yang kadaluarsa.");
        return self::SUCCESS;
    }
}