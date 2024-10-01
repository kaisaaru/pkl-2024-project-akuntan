<?php

namespace App\Repository\SuratJalan;

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
use App\Models\Kategori;
use App\Models\SubBukuBesar;
use App\Models\StokOpnemBarang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;

class SuratJalanRepository
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        if ($id === null) {
            return redirect('/dataSJ')->with('error', 'Terjadi Kesalahan');
        }
        try {
            $SuratOrder = OrderPenjualan::all();
            $SuratJalan = SuratJalan::paginate(20);
            $perusahaan = Perusahaan::all();
            $detailTotalso = 0;
            $detailBarangso = 0;
            $detailTotalsj = 0;
            $detailBarangsj = 0;
            $detail = []; // Initialize $detail as an empty array
            $detailsj = []; // Initialize $detail as an empty array
            $detailso = []; // Initialize $detail as an empty array

            foreach ($SuratOrder as $so) {
                $details = detail_op::where('id_so', $so->id_so)->first();
                if ($details) {
                    $detaillagi = detail_op::where('id_so', $so->id_so)->get();
                    $detailso[] = $detaillagi;
                }
            }

            foreach ($SuratJalan as $sj) {
                $details = detail_sj::where('id_sj', $sj->id_sj)->first();
                if ($details) {
                    $detaillagi = detail_sj::where('id_sj', $sj->id_sj)->get();
                    $detailsj[] = $detaillagi;
                }
            }

            return view('barang.barangkeluar.suratjalan.datasj', compact('SuratOrder', 'SuratJalan', 'perusahaan', 'detailTotalso', 'detailBarangso', 'detailTotalsj', 'detailBarangsj', 'detail', 'detailsj', 'detailso'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function laporan()
    {
        try {
            $SuratJalan = SuratJalan::all();
            return view('barang.barangkeluar.suratjalan.dataSJ', compact('SuratJalan'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    public function print($id_so, $id_sj)
    {
        try {
            $so = OrderPenjualan::where('id_so', $id_so)->first();
            $alamat = Perusahaan::find($so->nama_perusahaan);
            $sj = SuratJalan::where('id_sj', $id_sj)->first();
            $perusahaan = $sj->perusahaan;
            $alamatGudang = $perusahaan->alamat_gudang;
            $detail = []; // Initialize $detail as an empty array
            $barang = []; // Initialize $barang as an empty array
            $supplier = $so->nama_perusahaan;
            $alamatsupplier = $perusahaan->alamat_gudang ?? null;


            $details = detail_sj::where('id_sj', $sj->id_sj)->with('barang')->latest()->first();
            if ($details) {
                $detaillagi = detail_sj::where('id_so', $sj->id_so)->get();
                $detail[] = $detaillagi;
                $kodeBarangArray = $details->barang->kode_barang;
                foreach ($detaillagi as $detailItem) {
                    $barang = Barang::where('barang_id', $detailItem->barang_id)->first();
                }
            }

            return view('barang.barangkeluar.suratjalan.print', compact('alamat', 'sj', 'perusahaan', 'details', 'detaillagi', 'detail', 'so', 'supplier', 'alamatGudang'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Store a newly created resource in storage.
     */

    public function SuratJalan()
    {
        try {
            $latestSuratJalan = SuratJalan::latest()->first();

            // Calculate the next ID
            $nextId = $latestSuratJalan ? ($latestSuratJalan->id + 1) : 1;

            // Format the ID for the purchase order
            $idFormatted = str_pad($nextId, 4, '0', STR_PAD_LEFT);
            $SuratJalanId = 'SJ-' . $idFormatted;
            $tanggalHariIni = Carbon::now()->setTimezone('Asia/Jakarta')->toDateString();

            $SuratOrders = OrderPenjualan::where('id_sj', null)->get();
            $perusahaan = Perusahaan::all();
            $detailTotal = 0;
            $detailBarang = 0;
            $perusahaan = Perusahaan::all();
            $detail = [];
            $barang = [];
            $PerusahaanOptions = Barang::select('barangs.Perusahaan as barang_id', 'barangs.nama_barang', 'perusahaans.kode_perusahaan as Perusahaan_id', 'perusahaans.nama_perusahaan as Perusahaan_nama')
                ->leftJoin('perusahaans', 'barangs.Perusahaan', '=', 'perusahaans.kode_perusahaan')
                ->get();

            foreach ($SuratOrders as $SuratOrder) {
                $details = detail_op::where('id_so', $SuratOrder->id_so)->first();
                if ($details) {
                    $detaillagi = detail_op::where('id_so', $SuratOrder->id_so)->get();
                    $detail[] = $detaillagi;
                    foreach ($detaillagi as $detailItem) {
                        $barang = Barang::where('barang_id', $detailItem->barang_id)->get();
                    }
                }
            }

            if ($SuratOrders->isEmpty()) {
                return redirect('/dataSJ')->with('error', 'Data purchase order tidak ditemukan');
            } else {
                return view('barang.barangkeluar.suratjalan.sj', compact('SuratOrders', 'detailTotal', 'detailBarang', 'detail', 'perusahaan', 'SuratJalanId', 'tanggalHariIni', 'barang', 'PerusahaanOptions', 'perusahaan'));
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function store(StoreSuratJalanRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SuratJalan $suratJalan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        try {
            $request->validate([
                'nama_barang' => 'required',
                'jumlah_barang' => 'required|numeric',
                // Pastikan jumlah_barang adalah angka
            ]);

            $suratJalan = SuratJalan::where('id_sj', $id)->first();
            if (!$suratJalan) {
                return redirect()->back()->with('error', 'Data surat jalan tidak ditemukan.');
            }

            $nyariperusahaan = Perusahaan::where('kode_perusahaan', $suratJalan->nama_perusahaan)->first();
            $barang = Barang::where('nama_barang', $suratJalan->nama_barang)->where('Perusahaan', $nyariperusahaan->kode_perusahaan)->first();
            $ceklagi = Barang::where('barang_id', $barang->barang_id)->where('Perusahaan', $barang->Perusahaan)->first();

            $barang = Barang::where('nama_barang', $suratJalan->nama_barang)->first();
            if (!$barang) {
                return redirect()->back()->with('error', 'Barang tidak ditemukan.');
            }

            $harga_jual = $ceklagi->harga_jual;
            $jumlah_harga = $harga_jual * $request->jumlah_barang;
            $total_bayar = $jumlah_harga - ($request->diskon / 100) * $jumlah_harga;


            $suratJalan->update([
                'nama_barang' => $request->nama_barang,
                'jumlah_barang' => $request->jumlah_barang,
                'total_bayar' => $total_bayar,
            ]);

            return redirect('/dataSJ')->with('success', 'Data berhasil diubah');
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        // Validasi input

    }

    public function createSuratJalan(Request $request)
    {
        try {
            $validateData = $request->validate([
                'ID_SO' => 'required',
                'tanggal_sj' => 'required|date',
                'nopol' => 'required|string',
                'nama_supir' => 'required|string',
                'ket' => 'required|string',
            ]);

            $so = OrderPenjualan::where('id_so', $validateData['ID_SO'])->first();
            $perusahaan = $so->kode_perusahaan;

            $sj = $request->input('result');

            $selectedItem = json_decode($sj, true);
            $id = auth()->user();
            $user = auth()->user()->name;
            $ids = $id->id;

            $lur = sj_line::create([
                'user_id' => $ids,
                'hariini' => $validateData['tanggal_sj'],

            ]);

            SuratJalan::create([
                'id_sj' => $lur->id_sj,
                'id_so' => $validateData['ID_SO'],
                'user' => $user,
                'tanggal_sj' => $validateData['tanggal_sj'],
                'nopol' => $validateData['nopol'],
                'nama_supir' => $validateData['nama_supir'],
                'ket' => $validateData['ket'],
                'jatuh_tempo' => $so->jatuh_tempo, // Isi field jatuh_tempo dari purchase_order
                'kode_perusahaan' => $so->kode_perusahaan,
                'nama_perusahaan' => $so->nama_perusahaan,
            ]);

            if ($lur) {
                if (is_array($selectedItem)) {
                    //cuman karena ini kan ngirim kalo itu kan ngambil terus ngirim, berarti problemnya di ngambilnya 
                    foreach ($selectedItem as $item) {
                        if (isset($item['id'])) {
                            $detail_so = detail_op::where('id', $item['id'])->where('id_so', $validateData['ID_SO'])->first();
                            if ($detail_so) {
                                $total = $item['quantity'] * $detail_so->harga * (1 - $detail_so->discount / 100);
                                detail_sj::create([
                                    'id_so' => $validateData['ID_SO'],
                                    'id_sj' => $lur->id_sj,
                                    'barang_id' => $detail_so->barang_id,
                                    'nama_barang' => $detail_so->nama_barang,
                                    'satuan' => $detail_so->satuan,
                                    'stok' => $item['quantity'],
                                    'harga' => $item['harga'],
                                    'potongan' => $item['potongan'],
                                    'diskon' => $item['diskon'],
                                    'harga_beli' => $detail_so->harga_beli,
                                ]);

                                $lastBalance = StokOpnemBarang::where('kode_barang', $detail_so->barang_id)
                                    ->orderBy('id', 'desc')
                                    ->firstOrNew();
                                $saldoTerakhir = ($lastBalance) ? $lastBalance->stok : 0;


                                // Update Barang table
                                Barang::where('barang_id', $detail_so->barang_id)->decrement('stok', $item['quantity']);

                                // Update kategori
                                $barang = Barang::where('barang_id', $detail_so->barang_id)->first();
                                $kategori = $barang->kategori;
                                Kategori::where('kode_kategori', $kategori)->decrement('stok', $item['quantity']);

                                $dok = 'SJ';
                                $ket = $perusahaan;

                                StokOpnemBarang::create([
                                    'kode_barang' => $detail_so->barang_id,
                                    'tanggal' => $validateData['tanggal_sj'],
                                    'no_bukti' => $lur->id_sj,
                                    'dok' => $dok,
                                    'ket' => $ket,
                                    'kredit' => $item['quantity'],
                                    'debet' => 0,
                                    'stok' => $saldoTerakhir - $item['quantity'],
                                    'harga' => $item['harga'], // Replace with the actual value for 'harga'
                                ]);
                            } else {
                                dd($detail_so);
                            }
                        } else {
                            dd($selectedItem);
                        }
                    }
                } else {
                    if (isset($selectedItem['id'])) {
                        $detail_so = detail_op::where('id', $selectedItem['id'])->where('id_so', $validateData['ID_SO'])->first();
                        if ($detail_so) {
                            $total = $selectedItem['quantity'] * $detail_so->harga * (1 - $detail_so->discount / 100);
                            detail_sj::create([
                                'id_so' => $validateData['ID_SO'],
                                'id_sj' => $lur->id_sj,
                                'barang_id' => $detail_so->barang_id,
                                'nama_barang' => $detail_so->nama_barang,
                                'satuan' => $detail_so->satuan,
                                'stok' => $selectedItem['quantity'],
                                'harga' => $selectedItem['harga'],
                                'potongan' => $selectedItem['potongan'],
                                'diskon' => $selectedItem['diskon'],
                                'harga_beli' => $detail_so->harga_beli,
                            ]);
                        } else {
                            dd($detail_so);
                        }
                    } else {
                        dd($selectedItem);
                    }
                }
            }
            $latestSuratJalan = SuratJalan::latest()->first();
            OrderPenjualan::where('id_so', $validateData['ID_SO'])->update(['id_sj' => $latestSuratJalan->id_sj]);

            return redirect('/dataSJ')->with('success', 'sj berhasil ditambah');
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSuratJalanRequest $request, SuratJalan $suratJalan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SuratJalan $suratJalan)
    {
        //
    }

    public function status($status, $id)
    {
        if ($status == 'Approve') {
            try {
                // // Ambil semua entri stok berdasarkan id_pb
                // $listStok = detail_sj::where('id_sj', $id)->get();

                // // Loop melalui setiap entri stok
                // foreach ($listStok as $stok) {
                //     // Tambahkan stok ke setiap barang
                //     $barang = Barang::where('barang_id', $stok->barang_id)->first();

                //     if ($barang) {
                //         $barang->stok -= $stok->stok;
                //         $barang->save();
                //     }
                // }

                SuratJalan::where('id_sj', $id)->update(['status' => $status]);
                return redirect('/dataSJ')->with('status', 'Data berhasil di setujui');
            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
        if ($status == 'Decline') {
            try {
                SuratJalan::where('id_sj', $id)->update(['status' => $status]);
                return redirect('/dataSJ')->with('status', 'Data berhasil di tolak');
            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        } else {
            return redirect('/dataSJ')->with('error', 'Terjadi kesalahan');
        }
    }

    public function faktur($id_sj)
    {
        try {
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

            return view('barang.barangkeluar.faktur.fakturjual', compact('FakturJualID', 'id_sj', 'sos', 'detailTotal', 'detailBarang', 'detail', 'perusahaan', 'tanggalHariIni', 'sj', 'akun', 'details', 'detaillagi'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
