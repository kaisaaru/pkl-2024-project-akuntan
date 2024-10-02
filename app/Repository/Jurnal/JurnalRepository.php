<?php

namespace App\Repository\Jurnal;

use App\Models\Perusahaan;
use App\Models\SuratJalan;
use App\Models\OrderPenjualan;
use App\Http\Requests\StoreSuratJalanRequest;
use App\Http\Requests\UpdateSuratJalanRequest;
use App\Models\Barang;
use App\Models\detail_op;
use App\Models\detail_sj;
use App\Models\faktur_jual;
use App\Models\sj_line;
use App\Models\BukuBesar;
use App\Models\SubBukuBesar;
use App\Models\RiwayatBukuBesar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\TipeAkun;
use App\Models\Neraca;
use App\Models\Jurnal;
use Illuminate\Support\Facades\Auth;
use Exception;

class JurnalRepository
{
    public function jurnal($id_sj)
    {

        $latestFakturJual = faktur_jual::latest()->first();

        // Calculate the next ID
        $nextId = $latestFakturJual ? ($latestFakturJual->id + 1) : 1;

        // Format the ID for the purchase order
        $idFormatted = str_pad($nextId, 4, '0', STR_PAD_LEFT);
        $FakturJualID = 'FJ-' . $idFormatted;
        $tanggalHariIni = Carbon::now()->setTimezone('Asia/Jakarta')->toDateString();

        $sos = OrderPenjualan::where('id_sj', $id_sj)->get();
        $perusahaan = Perusahaan::all();
        $detailTotal = 0;
        $detailBarang = 0;
        $detail = []; // Initialize $detail as an empty array


        $sj = SuratJalan::where('id_sj', $id_sj)->first();

        $detail = []; // Initialize $detail as an empty array
        $barang = []; // Initialize $barang as an empty array

        $details = detail_sj::where('id_sj', $sj->id_sj)->with('barang')->latest()->first();
        if ($details) {
            $detaillagi = detail_sj::where('id_so', $sj->id_so)->get();
            $detail[] = $detaillagi;
        }

        $akun = BukuBesar::with('subBukuBesar')->get();

        $tipe = TipeAkun::all();
        $data = BukuBesar::all();
        $BukuBesar = BukuBesar::orderBy('no_bukubesar', 'asc')->get();

        // Add this line to get the previous no_bukubesar
        $previousNoBukuBesar = BukuBesar::orderBy('no_bukubesar', 'desc')->value('no_bukubesar');

        $bukubesar = BukuBesar::all();

        // Add this line to get the previous no_bukubesar
        $previousNoSubBukuBesar = SubBukuBesar::orderBy('no_subbukubesar', 'desc')->value('no_subbukubesar');

        return view('barang.barangkeluar.jurnal.jurnal', compact('FakturJualID', 'id_sj', 'sos', 'detailTotal', 'detailBarang', 'detail', 'perusahaan', 'tanggalHariIni', 'sj', 'akun', 'details', 'detaillagi', 'data', 'tipe', 'BukuBesar', 'previousNoBukuBesar', 'bukubesar', 'previousNoSubBukuBesar'));
    }

    public function create($validatedData, $id_sj)
    {
        try {
            // Buku Besar
            $dok = 'SJ';
            $ketlaporan = 'No.';
            $sj = SuratJalan::where('id_sj', $id_sj)->first();
            $ketlaporan .= ' ' . $id_sj . '  ' . $sj->nama_perusahaan;

            foreach ($validatedData['no_subbukubesar'] as $index => $no_subbukubesar) {
                $kredit = $validatedData['kredit'][$index];
                $debit = $validatedData['debit'][$index];
                $subBukuBesar = SubBukuBesar::with(['bukubesars', 'bukubesars.tipeAkun'])->where('no_subbukubesar', $no_subbukubesar)->first();

                $subBukuBesar->update([
                    'debet' => $subBukuBesar->debet + $debit,
                    'kredit' => $subBukuBesar->kredit + $kredit
                ]);

                foreach ($subBukuBesar->bukubesars as $bukuBesar) {
                    $bukuBesar->update([
                        'debet' => $bukuBesar->debet + $debit,
                        'kredit' => $bukuBesar->kredit + $kredit,
                    ]);
                }

                $BukuBesars = $subBukuBesar->bukubesars;

                if ($subBukuBesar) {
                    foreach ($BukuBesars as $BukuBesar) {
                        $tipe = $bukuBesar->tipe;
                        // dump($tipe);
                        $timbangan = TipeAkun::where('tipe', $tipe)->first();
                        $neraca = $timbangan->jenis;
                        if ($BukuBesars->isNotEmpty()) {
                            $no_bukubesar = $BukuBesar->no_bukubesar;

                            $saldo_terakhir_entry = RiwayatBukuBesar::where('no_subbukubesar', $no_subbukubesar)
                                ->orderBy('id', 'desc')
                                ->firstOrNew();

                            $saldo_terakhir = $saldo_terakhir_entry ? $saldo_terakhir_entry->saldo_kumulatif : 0;

                            $total = $saldo_terakhir + $debit - $kredit;

                            RiwayatBukuBesar::create([
                                'tanggal' => $validatedData['tanggal'],
                                'no_bukubesar' => $no_bukubesar,
                                'ket_bukubesar' => $BukuBesar->ket,
                                'no_subbukubesar' => $no_subbukubesar,
                                'ket_subbukubesar' => $subBukuBesar->ket,
                                'dok' => $dok,
                                'no_referensi' => $id_sj,
                                'ket' => $ketlaporan,
                                'debet' => $debit,
                                'kredit' => $kredit,
                                'saldo_kumulatif' => $total,
                                'created_at' => $validatedData['tanggal'],
                            ]);

                            $saldoTerakhir = RiwayatBukuBesar::where('no_subbukubesar', $no_subbukubesar)
                                ->orderBy('id', 'desc')
                                ->value('saldo_kumulatif');
                            // dump($saldoTerakhir);

                            $subBukuBesar->update([
                                'jumlah' => $saldoTerakhir,
                            ]);
                        } else {
                            // Menangani kasus jika BukuBesar tidak ditemukan
                        }
                    }
                } else {
                    // Menangani kasus jika SubBukuBesar tidak ditemukan
                }

                // Update Buku Besar
                $totalJumlahBukuBesar = SubBukuBesar::where('no_bukubesar', $no_bukubesar)
                    ->sum('jumlah');

                BukuBesar::where('no_bukubesar', $no_bukubesar)->update([
                    'jumlah' => $totalJumlahBukuBesar
                ]);

                // Update Tipe
                $totalJumlahTipe = BukuBesar::where('tipe', $tipe)
                    ->sum('jumlah');

                TipeAkun::where('tipe', $tipe)->update([
                    'jumlah' => $totalJumlahTipe
                ]);

                // Update Neraca
                $totalJumlahNeraca = TipeAkun::where('jenis', $neraca)
                    ->sum('jumlah');

                Neraca::where('neraca', $neraca)->update([
                    'jumlah' => $totalJumlahNeraca
                ]);

                // Ekuitas
                $totalpenjualan = RiwayatBukuBesar::where('no_bukubesar', 112)
                    ->sum('kredit');

                $pbdawal = RiwayatBukuBesar::where('no_bukubesar', 103)
                    ->oldest('created_at') // Urutkan berdasarkan tanggal secara ascending (terlama dulu)
                    ->first();

                // $saldo_kumulatif_pbdawal = $pbdawal ? $pbdawal->saldo_kumulatif : 0;
                $saldo_kumulatif_pbdawal = 0;

                $pembelian = RiwayatBukuBesar::where('no_bukubesar', 103)
                    ->sum('debet');

                $pbdakhir = RiwayatBukuBesar::where('no_bukubesar', 103)
                    ->latest('created_at')
                    ->first();

                $saldo_kumulatif_pbdakhir = $pbdakhir ? $pbdakhir->saldo_kumulatif : 0;

                $totalphp = 0;
                $totalphp = $saldo_kumulatif_pbdawal + $pembelian - $saldo_kumulatif_pbdakhir;

                $labakotor = 0;
                $labakotor = $totalpenjualan - $totalphp;

                $biayalain = BukuBesar::where('tipe', 'Biaya Lain')->first();
                $biaya_lain = $biayalain->jumlah;
                $lababersih = $labakotor - $biaya_lain;

                $ketekuitas = "Laba Periode Berjalan";

                $ekuitas = SubBukuBesar::where('ket', $ketekuitas)->first();

                // dump($lababersih);
                if ($ekuitas) {
                    $no_subbukubesarekuitas = $ekuitas->no_subbukubesar;
                    $ekuitas->update([
                        'jumlah' => $lababersih,
                    ]);

                    $no_bukubesarekuitas = $ekuitas->no_bukubesar;

                    $bukubesarekuitas = BukuBesar::where('no_bukubesar', $no_bukubesarekuitas)->first();

                    $tipe_ekuitas = $bukubesarekuitas->tipe;

                    $tipeekuitas = TipeAkun::where('tipe', $tipe_ekuitas)->first();

                    $neracaekuitas = $tipeekuitas->jenis;

                    // Update Buku Besar
                    $totalEkuitasBukuBesar = SubBukuBesar::where('no_bukubesar', $no_bukubesarekuitas)
                        ->sum('jumlah');

                    BukuBesar::where('no_bukubesar', $no_bukubesarekuitas)->update([
                        'jumlah' => $totalEkuitasBukuBesar
                    ]);

                    // Update Tipe
                    $totalJumlahTipeEkuitas = BukuBesar::where('tipe', $tipe_ekuitas)
                        ->sum('jumlah');

                    TipeAkun::where('tipe', $tipe_ekuitas)->update([
                        'jumlah' => $totalJumlahTipeEkuitas
                    ]);

                    // Update Neraca
                    $totalJumlahNeracaEkuitas = TipeAkun::where('jenis', $neracaekuitas)
                        ->sum('jumlah');

                    Neraca::where('neraca', $neracaekuitas)->update([
                        'jumlah' => $totalJumlahNeracaEkuitas
                    ]);
                }
            }

            SuratJalan::where('id_sj', $id_sj)->update(['status' => 'Jurnal']);
            return redirect('/dataSJ')->with('success', 'Jurnal berhasil ditambah');
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function inputlain(Request $request)
    {
        try {
            $latestPurchaseOrder = Jurnal::latest()->first();

            // Calculate the next ID
            $nextId = $latestPurchaseOrder ? ($latestPurchaseOrder->id + 1) : 1;

            // Format the ID for the purchase order
            $idFormatted = str_pad($nextId, 4, '0', STR_PAD_LEFT);
            $purchaseOrderId = 'J-' . $idFormatted;

            $akun = BukuBesar::with('subBukuBesar')->get();
            $tanggalHariIni = Carbon::now()->setTimezone('Asia/Jakarta')->toDateString();

            return view('jurnal.lain', compact('tanggalHariIni', 'akun', 'purchaseOrderId'));
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 400);
        }
    }

    public function jurnallain($validatedData)
    {
        try {
            $latestPurchaseOrder = Jurnal::latest()->first();

            // Calculate the next ID
            $nextId = $latestPurchaseOrder ? ($latestPurchaseOrder->id + 1) : 1;

            // Format the ID for the purchase order
            $idFormatted = str_pad($nextId, 4, '0', STR_PAD_LEFT);
            $purchaseOrderId = 'J-' . $idFormatted;

            // Buku Besar
            $dok = 'Jurnal Lain';
            $ketlaporan = 'Jurnal No.';
            $ketlaporan .= ' ' . $purchaseOrderId . ' ' . $validatedData['ketsj'];

            Jurnal::create([
                'tanggal' => $validatedData['tanggal'],
                'ket' => $validatedData['ketsj'],
            ]);

            foreach ($validatedData['no_subbukubesar'] as $index => $no_subbukubesar) {
                $kredit = $validatedData['kredit'][$index];
                $debit = $validatedData['debit'][$index];
                $ket = $validatedData['ket'][$index];
                $subBukuBesar = SubBukuBesar::with(['bukubesars', 'bukubesars.tipeAkun'])->where('no_subbukubesar', $no_subbukubesar)->first();

                $subBukuBesar->update([
                    'debet' => $subBukuBesar->debet + $debit,
                    'kredit' => $subBukuBesar->kredit + $kredit
                ]);

                foreach ($subBukuBesar->bukubesars as $bukuBesar) {
                    $bukuBesar->update([
                        'debet' => $bukuBesar->debet + $debit,
                        'kredit' => $bukuBesar->kredit + $kredit,
                    ]);
                }

                $BukuBesars = $subBukuBesar->bukubesars;

                if ($subBukuBesar) {
                    foreach ($BukuBesars as $BukuBesar) {
                        $tipe = $bukuBesar->tipe;
                        // dump($tipe);
                        $timbangan = TipeAkun::where('tipe', $tipe)->first();
                        $neraca = $timbangan->jenis;
                        if ($BukuBesars->isNotEmpty()) {
                            $no_bukubesar = $BukuBesar->no_bukubesar;

                            $saldo_terakhir_entry = RiwayatBukuBesar::where('no_subbukubesar', $no_subbukubesar)
                                ->orderBy('id', 'desc')
                                ->firstOrNew();

                            $saldo_terakhir = $saldo_terakhir_entry ? $saldo_terakhir_entry->saldo_kumulatif : 0;

                            $total = $saldo_terakhir + $debit - $kredit;

                            RiwayatBukuBesar::create([
                                'tanggal' => $validatedData['tanggal'],
                                'no_bukubesar' => $no_bukubesar,
                                'ket_bukubesar' => $BukuBesar->ket,
                                'no_subbukubesar' => $no_subbukubesar,
                                'ket_subbukubesar' => $subBukuBesar->ket,
                                'dok' => $dok,
                                'no_referensi' => $purchaseOrderId,
                                'ket' => $ket,
                                'debet' => $debit,
                                'kredit' => $kredit,
                                'saldo_kumulatif' => $total,
                                'created_at' => $validatedData['tanggal'],
                            ]);

                            $saldoTerakhir = RiwayatBukuBesar::where('no_subbukubesar', $no_subbukubesar)
                                ->latest()
                                ->value('saldo_kumulatif');
                            // dump($saldoTerakhir);

                            $subBukuBesar->update([
                                'jumlah' => $saldoTerakhir,
                            ]);
                        } else {
                            // Menangani kasus jika BukuBesar tidak ditemukan
                        }
                    }
                } else {
                    // Menangani kasus jika SubBukuBesar tidak ditemukan
                }

                // Update Buku Besar
                $totalJumlahBukuBesar = SubBukuBesar::where('no_bukubesar', $no_bukubesar)
                    ->sum('jumlah');

                BukuBesar::where('no_bukubesar', $no_bukubesar)->update([
                    'jumlah' => $totalJumlahBukuBesar
                ]);

                // Update Tipe
                $totalJumlahTipe = BukuBesar::where('tipe', $tipe)
                    ->sum('jumlah');

                TipeAkun::where('tipe', $tipe)->update([
                    'jumlah' => $totalJumlahTipe
                ]);

                // Update Neraca
                $totalJumlahNeraca = TipeAkun::where('jenis', $neraca)
                    ->sum('jumlah');

                Neraca::where('neraca', $neraca)->update([
                    'jumlah' => $totalJumlahNeraca
                ]);

                // Ekuitas
                $totalpenjualan = RiwayatBukuBesar::where('no_bukubesar', 112)
                    ->sum('kredit');

                $pbdawal = RiwayatBukuBesar::where('no_bukubesar', 103)
                    ->oldest('created_at') // Urutkan berdasarkan tanggal secara ascending (terlama dulu)
                    ->first();

                // $saldo_kumulatif_pbdawal = $pbdawal ? $pbdawal->saldo_kumulatif : 0;
                $saldo_kumulatif_pbdawal = 0;

                $pembelian = RiwayatBukuBesar::where('no_bukubesar', 103)
                    ->sum('debet');

                $pbdakhir = RiwayatBukuBesar::where('no_bukubesar', 103)
                    ->latest('created_at')
                    ->first();

                $saldo_kumulatif_pbdakhir = $pbdakhir ? $pbdakhir->saldo_kumulatif : 0;

                $totalphp = 0;
                $totalphp = $saldo_kumulatif_pbdawal + $pembelian - $saldo_kumulatif_pbdakhir;

                $labakotor = 0;
                $labakotor = $totalpenjualan - $totalphp;

                $biayalain = BukuBesar::where('tipe', 'Biaya Lain')->first();
                $biaya_lain = $biayalain->jumlah;
                $lababersih = $labakotor - $biaya_lain;

                $ketekuitas = "Laba Periode Berjalan";

                $ekuitas = SubBukuBesar::where('ket', $ketekuitas)->first();

                // dump($lababersih);
                if ($ekuitas) {
                    $no_subbukubesarekuitas = $ekuitas->no_subbukubesar;
                    $ekuitas->update([
                        'jumlah' => $lababersih,
                    ]);

                    $no_bukubesarekuitas = $ekuitas->no_bukubesar;

                    $bukubesarekuitas = BukuBesar::where('no_bukubesar', $no_bukubesarekuitas)->first();

                    $tipe_ekuitas = $bukubesarekuitas->tipe;

                    $tipeekuitas = TipeAkun::where('tipe', $tipe_ekuitas)->first();

                    $neracaekuitas = $tipeekuitas->jenis;

                    // Update Buku Besar
                    $totalEkuitasBukuBesar = SubBukuBesar::where('no_bukubesar', $no_bukubesarekuitas)
                        ->sum('jumlah');

                    BukuBesar::where('no_bukubesar', $no_bukubesarekuitas)->update([
                        'jumlah' => $totalEkuitasBukuBesar
                    ]);

                    // Update Tipe
                    $totalJumlahTipeEkuitas = BukuBesar::where('tipe', $tipe_ekuitas)
                        ->sum('jumlah');

                    TipeAkun::where('tipe', $tipe_ekuitas)->update([
                        'jumlah' => $totalJumlahTipeEkuitas
                    ]);

                    // Update Neraca
                    $totalJumlahNeracaEkuitas = TipeAkun::where('jenis', $neracaekuitas)
                        ->sum('jumlah');

                    Neraca::where('neraca', $neracaekuitas)->update([
                        'jumlah' => $totalJumlahNeracaEkuitas
                    ]);
                }
            }

            return redirect()->back();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
