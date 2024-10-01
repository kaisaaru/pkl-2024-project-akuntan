<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perusahaans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_perusahaan')->unique();
            $table->string('nama_perusahaan');
            $table->string('jenis')->enum('Supplier', 'Konsumen', 'Developer');
            $table->string('alamat_kantor');
            $table->string('alamat_gudang');
            $table->string('nama_pimpinan');
            $table->string('no_telepon');
            $table->decimal('plafon_debit', 10, 2)->nullable();
            $table->decimal('plafon_kredit', 10, 2)->nullable();
            $table->timestamps();
        });

        // Adjusting the timezone for created_at and updated_at columns
        Carbon::setLocale('id');
        Carbon::setTestNow(Carbon::now()->setTimezone('Asia/Jakarta'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perusahaans');
    }
};
