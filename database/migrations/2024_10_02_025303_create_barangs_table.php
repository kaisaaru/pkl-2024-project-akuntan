<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('barang_id')->unique();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('nama_barang');
            $table->string('satuan');
            $table->string('stok');
            $table->string('phisik')->default(0);
            $table->string('selisih')->default(0);
            $table->string('kategori');
            $table->string('kelompok');
            $table->bigInteger('harga_jual');
            $table->bigInteger('harga_beli');
            $table->string('Perusahaan');
            $table->foreign('Perusahaan')->references('kode_perusahaan')->on('perusahaans')->cascadeOnDelete();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
