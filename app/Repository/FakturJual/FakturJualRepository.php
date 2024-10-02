<?php

namespace App\Repository\FakturJual;

use App\Models\faktur_jual;
use App\Http\Requests\Storefaktur_jualRequest;
use App\Http\Requests\Updatefaktur_jualRequest;
use App\Models\detail_fj;
use App\Models\detail_sj;
use App\Models\Barang;
use App\Models\TipeAkun;
use App\Models\Neraca;
use App\Models\RiwayatBukuBesar;
use App\Models\fj_line;
use App\Models\BukuBesar;
use App\Models\Perusahaan;
use App\Models\SubBukuBesar;
use App\Models\SuratJalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FakturJualRepository
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $fj = faktur_jual::paginate(20);
            $detail = detail_fj::all();
            return view('barang.barangkeluar.faktur.dataFJ', compact('fj', 'detail'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createfj(Request $request, $id_sj)
    {

        try {
            $validatedData = $request->validate([
                'id_fj' => 'required',
                'tanggal_fj' => 'required',
                'id_sj' => 'required',
                'ketsj' => 'required',
                'total_penjualan' => 'required',
                'pembayaran' => 'required',
                "no_subbukubesar.*" => 'required',
                "kredit.*" => 'required',
                "debit.*" => 'required',
                "ket.*" => 'required'
            ]);
            $perusahaan = SuratJalan::where('id_sj', $id_sj)->first();
            $id = auth()->user();
            $ids = $id->id;
            $fj_line = fj_line::create([
                'user_id' => $ids,
                'hariini' => $validatedData['tanggal_fj'],

            ]);

            faktur_jual::create([
                'id_so' => $perusahaan->id_so,
                'id_fj' => $fj_line->id_fj,
                'id_sj' => $id_sj,
                'tanggal_fj' => $validatedData['tanggal_fj'],
                'ket' => $validatedData['ketsj'],
                'kode_perusahaan' => $perusahaan->kode_perusahaan,
                'nama_perusahaan' => $perusahaan->nama_perusahaan,
                'jatuh_tempo' => $perusahaan->jatuh_tempo,
                'total_penjualan' =>  $validatedData['total_penjualan'],
                'pembayaran' =>  $validatedData['pembayaran'],
            ]);

            foreach ($validatedData['no_subbukubesar'] as $nigga => $no_subbukubesar) {
                $result = SubBukuBesar::where('no_subbukubesar', $no_subbukubesar)->first();
                $no_bukubesar = $result->no_bukubesar;
                $ket_bukubesar = BukuBesar::where('no_bukubesar', $no_bukubesar)->first();

                $kredit = $validatedData['kredit'][$nigga];
                $debit = $validatedData['debit'][$nigga];
                $ket = $validatedData['ket'][$nigga];

                detail_fj::create([
                    'id_fj' => $fj_line->id_fj,
                    'id_sj' => $id_sj,
                    'no_bukubesar' => $no_bukubesar,
                    'ket_bukubesar' => $ket_bukubesar->ket,
                    'no_subbukubesar' => $no_subbukubesar,
                    'ket_subbukubesar' => $result->ket,
                    'debit' => $debit,
                    'kredit' => $kredit,
                    'ket' => $ket,
                    'kode_perusahaan' => $perusahaan->kode_perusahaan
                ]);
            }

            // Buku Besar
            $dok = 'FJ';
            $ketlaporan = 'Faktur Jual No.';
            $sj = SuratJalan::where('id_sj', $id_sj)->first();
            $nama_perusahaan = Auth::user()->perusahaan->nama_perusahaan;
            $ketlaporan .= ' ' . $validatedData['id_fj'] . '  ' . $sj->nama_perusahaan;

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

                        $timbangan = TipeAkun::where('tipe', $tipe)->first();
                        $neraca = $timbangan->jenis;
                        if ($BukuBesars->isNotEmpty()) {
                            $no_bukubesar = $BukuBesar->no_bukubesar;
                            $kredit_bukubesar = $BukuBesar->kredit;
                            $debet_bukubesar = $BukuBesar->debet;
                            $saldo_terakhir_entry = RiwayatBukuBesar::where('no_subbukubesar', $no_subbukubesar)
                                ->orderBy('id', 'desc')
                                ->firstOrNew();

                            $saldo_terakhir = $saldo_terakhir_entry ? $saldo_terakhir_entry->saldo_kumulatif : 0;

                            $total = $saldo_terakhir + $debit - $kredit;

                            $jumlah = $kredit + $kredit_bukubesar;
                            RiwayatBukuBesar::create([
                                'tanggal' => $validatedData['tanggal_fj'],
                                'no_bukubesar' => $no_bukubesar,
                                'ket_bukubesar' => $BukuBesar->ket,
                                'no_subbukubesar' => $no_subbukubesar,
                                'ket_subbukubesar' => $subBukuBesar->ket,
                                'dok' => $dok,
                                'no_referensi' => $validatedData['id_fj'],
                                'ket' => $ketlaporan,
                                'debet' => $debit,
                                'kredit' => $kredit,
                                'saldo_kumulatif' => $total,
                                'created_at' => $validatedData['tanggal_fj'],
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
                $totalJumlahBukuBesar = SubBukuBesar::where('no_bukubesar', $no_bukubesar)->sum('jumlah');
                BukuBesar::where('no_bukubesar', $no_bukubesar)->update(['jumlah' => $totalJumlahBukuBesar]);

                // Update Tipe
                $totalJumlahTipe = BukuBesar::where('tipe', $tipe)->sum('jumlah');
                TipeAkun::where('tipe', $tipe)->update(['jumlah' => $totalJumlahTipe]);

                // Update Neraca
                $totalJumlahNeraca = TipeAkun::where('jenis', $neraca)->sum('jumlah');
                Neraca::where('neraca', $neraca)->update(['jumlah' => $totalJumlahNeraca]);

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

            SuratJalan::where('id_sj', $id_sj)->update(['status' => 'faktur']);
            return redirect('/dataFJ')->with('success', 'FJ berhasil ditambah');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function print($id_fj, $id_sj)
    {
        try {
            $fj = faktur_jual::where('id_fj', $id_fj)->first();
            $sj = SuratJalan::where('id_sj', $id_sj)->first();
            $perusahaan = $sj->perusahaan;
            $alamatGudang = $perusahaan->alamat_gudang;
            $detail = []; // Initialize $detail as an empty array
            $barang = []; // Initialize $barang as an empty array

            $details = detail_sj::where('id_sj', $sj->id_sj)->with('barang')->latest()->first();
            if ($details) {
                $detaillagi = detail_sj::where('id_sj', $sj->id_sj)->get();
                $detail[] = $detaillagi;
                $kodeBarangArray = $details->barang->kode_barang;
                foreach ($detaillagi as $detailItem) {
                    $barang = Barang::where('barang_id', $detailItem->barang_id)->first();
                }
            }

            return view('barang.barangkeluar.faktur.print', compact('fj', 'sj', 'detail', 'barang', 'details', 'detaillagi', 'perusahaan', 'alamatGudang'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Storefaktur_jualRequest $request)
    {
        try {
            //
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(faktur_jual $faktur_jual)
    {
        try {
            //
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(faktur_jual $faktur_jual)
    {
        try {
            //
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Updatefaktur_jualRequest $request, faktur_jual $faktur_jual)
    {
        try {
            //
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(faktur_jual $faktur_jual)
    {
        try {
            //
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function status($status, $id)
    {
        try {
            if ($status == 'Approve') {
                faktur_jual::where('id_fj', $id)->update(['status' => $status]);
                return redirect('/dataPB')->with('status', 'Data berhasil di setujui');
            }
            if ($status == 'Dibatalkan') {
                faktur_jual::where('id_fj', $id)->update(['status' => $status]);
                return redirect('/dataPB')->with('status', 'Data berhasil di tolak');
            } else {
                return redirect('/dataPB')->with('error', 'Terjadi kesalahan');
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
