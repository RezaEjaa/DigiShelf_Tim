<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel sesi peminjaman (1 request bisa banyak buku)
        Schema::create('borrowing_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('qr_code')->unique();          // DIGI-XXXXXX
            $table->date('pickup_date');                   // tanggal pengambilan
            $table->date('return_date');                   // tanggal pengembalian
            $table->enum('status', ['pending', 'active', 'returned', 'cancelled'])->default('pending');
            $table->timestamp('expires_at')->nullable();   // auto cancel 24 jam
            $table->timestamp('verified_at')->nullable();  // saat admin scan QR
            $table->timestamp('returned_at')->nullable();  // saat admin kembalikan
            $table->timestamps();
        });

        // Tabel item buku per sesi peminjaman
        Schema::create('borrowing_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowing_request_items');
        Schema::dropIfExists('borrowing_requests');
    }
};