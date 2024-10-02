<?php

namespace App\Repository\FakturBeli;

use App\Http\Requests\Storefaktur_beliRequest;
use App\Http\Requests\Updatefaktur_beliRequest;
use App\Models\Barang;
use App\Models\BukuBesar;
use App\Models\detail_faktur;
use App\Models\detail_pb;
use App\Models\faktur_beli;
use App\Models\faktur_line;
use App\Models\Neraca;
use App\Models\PenerimaanBarang;
use App\Models\Perusahaan;
use App\Models\PurchaseOrder;
use App\Models\RiwayatBukuBesar;
use App\Models\SubBukuBesar;
use App\Models\TipeAkun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FakturBeliRepository
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $fb = faktur_beli::paginate(20);
            $detail = detail_faktur::with('subbukubesar')->get();
            return view('barang.barangmasuk.faktur.dataFB', compact('fb', 'detail'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createfb(Request $request, $id_pb)
    {
        try {
            $validatedData = $request->validate([
                'id_fb' => 'required',
                'tanggal_fb' => 'required',
                'id_pb' => 'required',
                'ketpb' => 'required',
                'total_pembelian' => 'required',
                'pembayaran' => 'required',
                "no_subbukubesar.*" => 'required',
                "kredit.*" => 'required',
                "debit.*" => 'required',
                "ket.*" => 'required'
            ]);
            // dd($validatedData);
            $id = auth()->user();
            $ids = $id->id;
            $fb_line = faktur_line::create([
                'user_id' => $ids,
                'hariini' => now()->toDateString()
            ]);
            $perusahaan = PenerimaanBarang::where('id_pb', $id_pb)->first();
            $pb = PenerimaanBarang::where('id_pb', $validatedData['id_pb'])->first();
            // $perusahaan = $pb->nama_perusahaan;
            $jatuh_tempo = $pb->jatuh_tempo;
            $po = $pb->id_po;

            faktur_beli::create([
                'id_po' => $po,
                'id_fb' => $fb_line->id_fb,
                'id_pb' => $id_pb,
                'tanggal_fb' => $validatedData['tanggal_fb'],
                'ket' => $validatedData['ketpb'],
                'kode_perusahaan' => $perusahaan->kode_perusahaan,
                'nama_perusahaan' => $perusahaan->nama_perusahaan,
                'jatuh_tempo' => $jatuh_tempo,
                'total_pembelian' =>  $validatedData['total_pembelian'],
                'pembayaran' =>  $validatedData['pembayaran'],
            ]);

            foreach ($validatedData['no_subbukubesar'] as $nigga => $no_subbukubesar) {
                $result = SubBukuBesar::where('no_subbukubesar', $no_subbukubesar)->first();
                $no_bukubesar = $result->no_bukubesar;
                $ket_bukubesar = BukuBesar::where('no_bukubesar', $no_bukubesar)->first();

                $kredit = $validatedData['kredit'][$nigga];
                $debit = $validatedData['debit'][$nigga];
                $ket = $validatedData['ket'][$nigga];


                detail_faktur::create([
                    'id_fb' => $fb_line->id_fb,
                    'id_pb' => $id_pb,
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

            $dok = 'FB';
            $ketlaporan = 'Faktur Beli No.';
            $po = PurchaseOrder::where('id_pb', $id_pb)->first();
            $nama_perusahaan = auth()->user()->perusahaan->nama_perusahaan;
            $ketlaporan .= ' ' . $validatedData['id_fb'] . '  ' . $po->nama_perusahaan;

            foreach ($validatedData['no_subbukubesar'] as $index => $no_subbukubesar) {
                $kredit = $validatedData['kredit'][$index];
                $debit = $validatedData['debit'][$index];
                $subBukuBesar = SubBukuBesar::with(['bukubesars', 'bukubesars.tipeAkun'])->where('no_subbukubesar', $no_subbukubesar)->first();

                $subBukuBesar->update([
                    'debet' => $subBukuBesar->debet + $debit,
                    'kredit' => $subBukuBesar->kredit - $kredit
                ]);

                foreach ($subBukuBesar->bukubesars as $bukuBesar) {

                    $bukuBesar->update([
                        'debet' => $bukuBesar->debet + $debit,
                        'kredit' => $bukuBesar->kredit - $kredit,
                    ]);
                }

                $BukuBesars = $subBukuBesar->bukubesars;

                if ($subBukuBesar) {
                    foreach ($BukuBesars as $BukuBesar) {
                        $tipe = $BukuBesar->tipe;

                        $timbangan = TipeAkun::where('tipe', $tipe)->first();
                        $neraca = $timbangan->jenis;
                        // dump($neraca);
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
                                'tanggal' => $validatedData['tanggal_fb'],
                                'no_bukubesar' => $no_bukubesar,
                                'ket_bukubesar' => $BukuBesar->ket,
                                'no_subbukubesar' => $no_subbukubesar,
                                'ket_subbukubesar' => $subBukuBesar->ket,
                                'dok' => $dok,
                                'no_referensi' => $validatedData['id_fb'],
                                'ket' => $ketlaporan,
                                'debet' => $debit,
                                'kredit' => $kredit,
                                'saldo_kumulatif' => $total,
                                'created_at' => $validatedData['tanggal_fb'],
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

            PenerimaanBarang::where('id_pb', $id_pb)->update(['status' => 'faktur']);
            return redirect('/dataFB')->with('success', 'FB berhasil ditambah');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function print($id_fb, $id_pb)
    {

        // $fb = faktur_beli::where('id_fb', $id_fb)->first();
        // $pb = PenerimaanBarang::where('id_pb', $id_pb)->first();
        // $perusahaan = $pb->nama_perusahaan;
        // // Use first() or get() to retrieve the data from the database
        // $perusahaanData = Perusahaan::where('nama_perusahaan', $perusahaan)->first();

        // // $alamatGudang = $perusahaan->alamat_gudang;
        // $detail = []; // Initialize $detail as an empty array
        // $barang = []; // Initialize $barang as an empty array

        // $details = detail_pb::where('id_pb', $pb->id_pb)->with('barang')->latest()->first();
        // if ($details) {
        //     $detaillagi = detail_pb::where('id_po', $pb->id_po)->get();
        //     $detail[] = $detaillagi;
        // }

        // return view('barang.barangmasuk.faktur.print', compact('fb', 'pb', 'detail', 'barang', 'details', 'detaillagi', 'perusahaan', 'perusahaanData'));

        try {
            $fb = faktur_beli::where('id_fb', $id_fb)->first();
            $pb = PenerimaanBarang::where('id_pb', $id_pb)->first();
            $id_po = $pb->id_po;
            $user = Auth::user();
            $purchaseOrders = PurchaseOrder::where('id_po', $id_po)->first();
            $supplier = $purchaseOrders->nama_perusahaan;
            $perusahaan = Perusahaan::where('nama_perusahaan', $supplier)->first();
            $perusahaankita = Perusahaan::where('kode_perusahaan', $user->kode_perusahaan)->first();
            // $perusahaan = $pb->perusahaan;
            // $alamatGudang = $perusahaan->alamat_gudang;
            $detail = []; // Initialize $detail as an empty array
            $barang = []; // Initialize $barang as an empty array

            $details = detail_pb::where('id_pb', $pb->id_pb)->with('barang')->latest()->first();
            if ($details) {
                $detaillagi = detail_pb::where('id_po', $pb->id_po)->get();
                $detail[] = $detaillagi;
            }
            return view('barang.barangmasuk.faktur.print', compact('fb', 'pb', 'detail', 'barang', 'details', 'detaillagi', 'perusahaan', 'perusahaankita'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Storefaktur_beliRequest $request)
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
    public function show(faktur_beli $faktur_beli)
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
    public function edit(faktur_beli $faktur_beli)
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
    public function update(Updatefaktur_beliRequest $request, faktur_beli $faktur_beli)
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
    public function destroy(faktur_beli $faktur_beli)
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
                faktur_beli::where('id_fb', $id)->update(['status' => $status]);
                return redirect('/dataPB')->with('status', 'Data berhasil di setujui');
            }
            if ($status == 'Dibatalkan') {
                faktur_beli::where('id_fb', $id)->update(['status' => $status]);
                return redirect('/dataPB')->with('status', 'Data berhasil di tolak');
            } else {
                return redirect('/dataPB')->with('error', 'Terjadi kesalahan');
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        // if ($status == 'Dibatalkan') {
        //     faktur_beli::where('id_fb', $id)->update(['status' => $status]);
        //     return redirect('/dataPB')->with('status', 'Data berhasil di tolak');
        // } else {
        //     return redirect('/dataPB')->with('error', 'Terjadi kesalahan');
        // }

    }
}
