<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\User' => 'App\Policies\UserPolicy',
        'App\Models\Barang' => 'App\Policies\BarangPolicy',
        'App\Models\BukuBesar' => 'App\Policies\BukuBesarPolicy',
        'App\Models\PurchaseOrder' => 'App\Policies\PurchaseOrderPolicy',
        'App\Models\PenerimaanBarang' => 'App\Policies\PenerimaanBarangPolicy',
        'App\Models\SuratJalan' => 'App\Policies\SuratJalanPolicy',
        'App\Models\OrderPenjualan' => 'App\Policies\OrderPenjualanPolicy',
        'App\Models\DetailFaktur' => 'App\Policies\DetailFakturPolicy',
        'App\Models\DetailOp' => 'App\Policies\DetailOpPolicy',
        'App\Models\DetailPb' => 'App\Policies\DetailPbPolicy',
        'App\Models\DetailPo' => 'App\Policies\DetailPoPolicy',
        'App\Models\DetailSj' => 'App\Policies\DetailSjPolicy',
        'App\Models\FakturBeli' => 'App\Policies\FakturBeliPolicy',
        'App\Models\FakjurJual' => 'App\Policies\FakjurJualPolicy',
        'App\Models\FakturLine' => 'App\Policies\FakturLinePolicy',
        'App\Models\FjLine' => 'App\Policies\FjLinePolicy',
        'App\Models\InformasiPerusahaan' => 'App\Policies\InformasiPerusahaanPolicy',
        'App\Models\JenisBarang' => 'App\Policies\JenisBarangPolicy',
        'App\Models\Kategori' => 'App\Policies\KategoriPolicy',
        'App\Models\Kelompok' => 'App\Policies\KelompokPolicy',
        'App\Models\Merek' => 'App\Policies\MerekPolicy',
        'App\Models\Neraca' => 'App\Policies\NeracaPolicy',
        'App\Models\Opline' => 'App\Policies\OplinePolicy',
        'App\Models\PbLine' => 'App\Policies\PbLinePolicy',
        'App\Models\Perusahaan' => 'App\Policies\PerusahaanPolicy',
        'App\Models\PoLine' => 'App\Policies\PoLinePolicy',
        'App\Models\RiwayatBukuBesar' => 'App\Policies\RiwayatBukuBesarPolicy',
        'App\Models\SjLine' => 'App\Policies\SjLinePolicy',
        'App\Models\SubBukuBesar' => 'App\Policies\SubBukuBesarPolicy',
        'App\Models\Termin' => 'App\Policies\TerminPolicy',
        'App\Models\TipeAkun' => 'App\Policies\TipeAkunPolicy'        
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
