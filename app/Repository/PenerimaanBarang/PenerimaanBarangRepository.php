<?php

namespace App\Repository\PenerimaanBarang;

use App\Models\SubBukuBesar;
use App\Models\detail_pb;
use App\Models\detail_po;
use App\Models\faktur_beli;
use App\Models\pb_line;
use Carbon\Carbon;
use App\Models\PenerimaanBarang;
use App\Models\PurchaseOrder;
use App\Models\Barang;
use App\Models\Perusahaan;
use App\Models\BukuBesar;
use App\Models\StokOpnemBarang;
use App\Models\Kategori;
use App\Models\hpp;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePenerimaanBarangRequest;
use App\Http\Requests\UpdatePenerimaanBarangRequest;
use Exception;
use Illuminate\Http\Request;

class PenerimaanBarangRepository
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        if ($id === null) {
            return redirect('/dataPB')->with('error', 'Terjadi Kesalahan');
        }
        try {
            $purchaseOrders = PurchaseOrder::all();
            $PenerimaanBarang = PenerimaanBarang::paginate(20);
            $perusahaan = Perusahaan::all();
            $detailTotalpo = 0;
            $detailBarangpo = 0;
            $detailTotalpb = 0;
            $detailBarangpb = 0;
            $detail = []; // Initialize $detail as an empty array
            $detailpb = []; // Initialize $detail as an empty array
            $detailpo = []; // Initialize $detail as an empty array

            foreach ($purchaseOrders as $purchaseOrder) {
                $details = detail_po::where('id_po', $purchaseOrder->id_po)->first();
                if ($details) {
                    $detaillagi = detail_po::where('id_po', $purchaseOrder->id_po)->get();
                    $detailpo[] = $detaillagi;
                }
            }

            foreach ($PenerimaanBarang as $pb) {
                $details = detail_pb::where('id_pb', $pb->id_pb)->first();
                if ($details) {
                    $detaillagi = detail_pb::where('id_pb', $pb->id_pb)->get();
                    $detailpb[] = $detaillagi;
                }
            }

            return view('barang.barangmasuk.pb.dataPB', compact('purchaseOrders', 'detailTotalpo', 'detailBarangpo', 'detailTotalpb', 'detailBarangpb', 'detailpb', 'detailpo', 'PenerimaanBarang', 'perusahaan'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function laporan()
    {
        try {
            $PenerimaanBarang = PenerimaanBarang::all();
            return view('barang.barangmasuk.pb.laporan', compact('PenerimaanBarang'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    public function print($id_po, $id_pb)
    {

        // $po = PurchaseOrder::where('id_po', $id_po)->first();
        // $pb = PenerimaanBarang::where('id_pb', $id_pb)->first();
        // $perusahaan = $pb->nama_perusahaan;
        // $perusahaanData = Perusahaan::where('nama_perusahaan', $perusahaan)->first();
        // $detail = []; // Initialize $detail as an empty array
        // $barang = []; // Initialize $barang as an empty array
        // $supplier = $po->nama_perusahaan;
        // $alamatsupplier = $perusahaan->alamat_gudang ?? null;

        try {
            $user = Auth::user();
            $purchaseOrders = PurchaseOrder::where('id_po', $id_po)->first();
            $supplier = $purchaseOrders->nama_perusahaan;
            $perusahaan = Perusahaan::where('nama_perusahaan', $supplier)->first();
            $perusahaankita = Perusahaan::where('kode_perusahaan', $user->kode_perusahaan)->first();
            $po = PurchaseOrder::where('id_po', $id_po)->first();
            $pb = PenerimaanBarang::where('id_pb', $id_pb)->first();

            // $alamatGudang = $perusahaan->alamat_gudang;
            $detail = []; // Initialize $detail as an empty array
            $barang = []; // Initialize $barang as an empty array

            $alamatsupplier = $perusahaan->alamat_gudang ?? null;

            $details = detail_pb::where('id_pb', $pb->id_pb)->with('barang')->latest()->first();
            if ($details) {
                $detaillagi = detail_pb::where('id_po', $pb->id_po)->get();
                $detail[] = $detaillagi;
                $kodeBarangArray = $details->barang->kode_barang;
                foreach ($detaillagi as $detailItem) {
                    $barang = Barang::where('barang_id', $detailItem->barang_id)->first();
                }
            }

            return view('barang.barangmasuk.pb.print', compact('pb', 'perusahaan', 'details', 'detaillagi', 'detail', 'po', 'supplier', 'alamatsupplier', 'perusahaan', 'perusahaankita'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        // return view('barang.barangmasuk.pb.print', compact('pb', 'perusahaan', 'details', 'detaillagi', 'detail', 'po', 'supplier', 'alamatsupplier', 'perusahaanData'));

    }

    public function PenerimaanBarang()
    {
        try {
            $latestPenerimaanBarang = PenerimaanBarang::latest()->first();

            // Calculate the next ID
            $nextId = $latestPenerimaanBarang ? ($latestPenerimaanBarang->id + 1) : 1;

            // Format the ID for the purchase order
            $idFormatted = str_pad($nextId, 4, '0', STR_PAD_LEFT);
            $PenerimaanBarangId = 'PB-' . $idFormatted;
            $tanggalHariIni = Carbon::now()->setTimezone('Asia/Jakarta')->toDateString();

            $purchaseOrders = PurchaseOrder::where('id_pb', null)->get();
            $perusahaan = Perusahaan::all();
            $detailTotal = 0;
            $detailBarang = 0;
            $perusahaan = Perusahaan::all();
            $detail = [];
            $barang = [];
            $PerusahaanOptions = Barang::select('barangs.Perusahaan as barang_id', 'barangs.nama_barang', 'perusahaans.kode_perusahaan as Perusahaan_id', 'perusahaans.nama_perusahaan as Perusahaan_nama')
                ->leftJoin('perusahaans', 'barangs.Perusahaan', '=', 'perusahaans.kode_perusahaan')
                ->get();

            foreach ($purchaseOrders as $purchaseOrder) {
                $details = detail_po::where('id_po', $purchaseOrder->id_po)->first();
                if ($details) {
                    $detaillagi = detail_po::where('id_po', $purchaseOrder->id_po)->get();
                    $detail[] = $detaillagi;
                    foreach ($detaillagi as $detailItem) {
                        $barang = Barang::where('barang_id', $detailItem->barang_id)->get();
                    }
                }
            }

            if ($purchaseOrders->isEmpty()) {
                return redirect('/dataPB')->with('error', 'Data purchase order tidak ditemukan');
            } else {
                return view('barang.barangmasuk.pb.penerimaanbarang', compact('purchaseOrders', 'detailTotal', 'detailBarang', 'detail', 'perusahaan', 'PenerimaanBarangId', 'tanggalHariIni', 'barang', 'PerusahaanOptions', 'perusahaan'));
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function createPenerimaanBarang(Request $request)
    {
        try {

            $validateData = $request->validate([
                'ID_PO' => 'required',
                'tanggal_pb' => 'required|date',
                'surat_jalan' => 'required|string',
                'ket' => 'required|string',
            ]);

            $purchaseOrder = PurchaseOrder::where('id_po', $validateData['ID_PO'])->first();
            $perusahaan = $purchaseOrder->kode_perusahaan;


            $pb = $request->input('result');
            //dd($pb);
            $selectedItem = json_decode($pb, true);
            // dd($selectedItem);
            $id = auth()->user();
            $ids = $id->id;

            $lur = pb_line::create([
                'user_id' => $ids,
                'hariini' => $validateData['tanggal_pb'],
            ]);



            if ($purchaseOrder) {
                $user = auth()->user()->name;

                PenerimaanBarang::create([
                    'id_pb' => $lur->id_pb,
                    'id_po' => $validateData['ID_PO'],
                    'user' => $user,
                    'tanggal_pb' => $validateData['tanggal_pb'],
                    'surat_jalan' => $validateData['surat_jalan'],
                    'ket' => $validateData['ket'],
                    'jatuh_tempo' => $purchaseOrder->jatuh_tempo, // Isi field jatuh_tempo dari purchase_order
                    'kode_perusahaan' => $purchaseOrder->kode_perusahaan, // Isi field jatuh_tempo dari purchase_order
                    'nama_perusahaan' => $purchaseOrder->nama_perusahaan, // Isi field jatuh_tempo dari purchase_order
                ]);
            } else {
                // Handle jika ID_PO tidak ditemukan
                // Misalnya, return response error
                return response()->json(['error' => 'Purchase Order not found'], 404);
            }


            if ($lur) {
                if (is_array($selectedItem)) {
                    //cuman karena ini kan ngirim kalo itu kan ngambil terus ngirim, berarti problemnya di ngambilnya 
                    foreach ($selectedItem as $item) {
                        if (isset($item['id'])) {
                            $detail_po = detail_po::where('id', $item['id'])->where('id_po', $validateData['ID_PO'])->first();
                            if ($detail_po) {
                                detail_pb::create([
                                    'id_po' => $validateData['ID_PO'],
                                    'id_pb' => $lur->id_pb,
                                    'barang_id' => $detail_po->barang_id,
                                    'nama_barang' => $detail_po->nama_barang,
                                    'satuan' => $detail_po->satuan,
                                    'stok' => $item['quantity'],
                                    'harga' => $item['harga'],
                                    'diskon' => $item['diskon'],
                                    'potongan' => $item['potongan'],
                                ]);

                                hpp::create([
                                    'barang_id' => $detail_po->barang_id,
                                    'referensi' => $lur->id_pb,
                                    'ket' => "PB",
                                    'stok' => $item['quantity'],
                                    'harga_beli' => $item['harga'],
                                    'sisa' => $item['quantity'],
                                ]);

                                $lastBalance = StokOpnemBarang::where('kode_barang', $detail_po->barang_id)
                                    ->orderBy('id', 'desc')
                                    ->firstOrNew();
                                $saldoTerakhir = ($lastBalance) ? $lastBalance->stok : 0;

                                // Update Barang table
                                Barang::where('barang_id', $detail_po->barang_id)->increment('stok', $item['quantity']);
                                // dd($pais);
                                $barang = Barang::where('barang_id', $detail_po->barang_id)->first();

                                $avg_harga_beli = HPP::where('barang_id', $detail_po->barang_id)
                                    ->where('ket', 'PB')
                                    ->avg('harga_beli');

                                // Memperbarui harga beli di tabel Barang
                                Barang::where('barang_id', $detail_po->barang_id)
                                    ->update(['harga_beli' => $avg_harga_beli]);

                                // Update kategori
                                // $barang = Barang::where('barang_id', $detail_po->barang_id)->first();
                                $kategori = $barang->kategori;
                                Kategori::where('kode_kategori', $kategori)->increment('stok', $item['quantity']);

                                $dok = 'PB';
                                $ket = $perusahaan;

                                StokOpnemBarang::create([
                                    'kode_barang' => $detail_po->barang_id,
                                    'tanggal' => $validateData['tanggal_pb'],
                                    'no_bukti' => $lur->id_pb,
                                    'dok' => $dok,
                                    'ket' => $ket,
                                    'debet' => $item['quantity'],
                                    'kredit' => 0,
                                    'stok' => $saldoTerakhir + $item['quantity'],
                                    'harga' => $item['harga'], // Replace with the actual value for 'harga'
                                ]);
                            } else {
                                dd($detail_po);
                            }
                        } else {
                            dd($selectedItem);
                        }
                    }
                } else {
                    if (isset($selectedItem['id'])) {
                        $detail_po = detail_po::where('id', $selectedItem['id'])->where('id_po', $validateData['ID_PO'])->first();
                        if ($detail_po) {
                            detail_pb::create([
                                'id_po' => $validateData['ID_PO'],
                                'id_pb' => $lur->id_pb,
                                'barang_id' => $detail_po->barang_id,
                                'nama_barang' => $detail_po->nama_barang,
                                'satuan' => $detail_po->satuan,
                                'stok' => $selectedItem['quantity'],
                                'harga' => $selectedItem['harga'],
                                'diskon' => $selectedItem['diskon'],
                                'potongan' => $selectedItem['potongan'],
                            ]);
                        } else {
                            dd($detail_po);
                        }
                    } else {
                        dd($selectedItem);
                    }
                }
            }

            $latestPenerimaanBarang = PenerimaanBarang::latest()->first();
            PurchaseOrder::where('id_po', $validateData['ID_PO'])->update(['id_pb' => $latestPenerimaanBarang->id_pb]);

            return redirect('/dataPB')->with('success', 'PB berhasil ditambah');
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function createPenerimaanBarangSeeder($validatedData)
    {
        try {

            $purchaseOrder = PurchaseOrder::where('id_po', $validatedData['ID_PO'])->first();
            $perusahaan = $purchaseOrder->kode_perusahaan;

            $pb = $validatedData['result'];
            //dd($pb);
            $selectedItem = json_decode($pb, true);
            // dd($selectedItem);

            $lur = pb_line::create([
                'user_id' => $validatedData['id'],
                'hariini' => $validatedData['tanggal_pb'],
            ]);

            if ($purchaseOrder) {
                PenerimaanBarang::create([
                    'id_pb' => $lur->id_pb,
                    'id_po' => $validatedData['ID_PO'],
                    'tanggal_pb' => $validatedData['tanggal_pb'],
                    'surat_jalan' => $validatedData['surat_jalan'],
                    'ket' => $validatedData['ket'],
                    'jatuh_tempo' => $purchaseOrder->jatuh_tempo, // Isi field jatuh_tempo dari purchase_order
                    'kode_perusahaan' => $purchaseOrder->kode_perusahaan, // Isi field jatuh_tempo dari purchase_order
                    'nama_perusahaan' => $purchaseOrder->nama_perusahaan, // Isi field jatuh_tempo dari purchase_order
                    'status' => 'Approve'
                ]);
            }

            if ($lur) {
                if (is_array($selectedItem)) {
                    //cuman karena ini kan ngirim kalo itu kan ngambil terus ngirim, berarti problemnya di ngambilnya 
                    foreach ($selectedItem as $item) {
                        if (isset($item['id'])) {
                            $detail_po = detail_po::where('id', $item['id'])->where('id_po', $validatedData['ID_PO'])->first();
                            if ($detail_po) {
                                detail_pb::create([
                                    'id_po' => $validatedData['ID_PO'],
                                    'id_pb' => $lur->id_pb,
                                    'barang_id' => $detail_po->barang_id,
                                    'nama_barang' => $detail_po->nama_barang,
                                    'satuan' => $detail_po->satuan,
                                    'stok' => $item['quantity'],
                                    'harga' => $item['harga'],
                                    'diskon' => $item['diskon'],
                                    'potongan' => $item['potongan'],
                                ]);

                                $lastBalance = StokOpnemBarang::where('kode_barang', $detail_po->barang_id)
                                    ->orderBy('tanggal', 'desc')
                                    ->first();

                                $saldoTerakhir = ($lastBalance) ? $lastBalance->stok : 0;

                                // Update Barang table
                                Barang::where('barang_id', $detail_po->barang_id)->increment('stok', $item['quantity']);
                                // dd($pais);
                                Barang::where('barang_id', $detail_po->barang_id)->update([
                                    'harga_beli' => $item['harga']
                                ]);

                                // Update kategori
                                $barang = Barang::where('barang_id', $detail_po->barang_id)->first();
                                $kategori = $barang->kategori;
                                Kategori::where('kode_kategori', $kategori)->increment('stok', $item['quantity']);

                                $dok = 'PB';
                                $ket = $perusahaan;

                                StokOpnemBarang::create([
                                    'kode_barang' => $detail_po->barang_id,
                                    'tanggal' => $validatedData['tanggal_pb'],
                                    'no_bukti' => $lur->id_pb,
                                    'dok' => $dok,
                                    'ket' => $ket,
                                    'debet' => $item['quantity'],
                                    'kredit' => 0,
                                    'stok' => $saldoTerakhir + $item['quantity'],
                                    'harga' => $item['harga'], // Replace with the actual value for 'harga'
                                ]);
                            } else {
                                dd($detail_po);
                            }
                        } else {
                            dd($selectedItem);
                        }
                    }
                } else {
                    if (isset($selectedItem['id'])) {
                        $detail_po = detail_po::where('id', $selectedItem['id'])->where('id_po', $validatedData['ID_PO'])->first();
                        if ($detail_po) {
                            detail_pb::create([
                                'id_po' => $validatedData['ID_PO'],
                                'id_pb' => $lur->id_pb,
                                'barang_id' => $detail_po->barang_id,
                                'nama_barang' => $detail_po->nama_barang,
                                'satuan' => $detail_po->satuan,
                                'stok' => $selectedItem['quantity'],
                                'harga' => $selectedItem['harga'],
                                'diskon' => $selectedItem['diskon'],
                                'potongan' => $selectedItem['potongan'],
                            ]);
                        } else {
                            dd($detail_po);
                        }
                    } else {
                        dd($selectedItem);
                    }
                }
            }

            $latestPenerimaanBarang = PenerimaanBarang::latest()->first();
            PurchaseOrder::where('id_po', $validatedData['ID_PO'])->update(['id_pb' => $latestPenerimaanBarang->id_pb]);
        } catch (Exception $e) {
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(PenerimaanBarang $penerimaanBarang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $request->validate([
            'nama_barang' => 'required',
            'jumlah_barang' => 'required|numeric',
        ]);
        try {
            $penerimaanBarang = PenerimaanBarang::where('id_pb', $id)->first();

            if (!$penerimaanBarang) {
                return redirect()->back()->with('error', 'Data penerimaan barang tidak ditemukan.');
            }

            $nyariperusahaan = Perusahaan::where('kode_perusahaan', $penerimaanBarang->nama_perusahaan)->first();
            $barang = Barang::where('nama_barang', $penerimaanBarang->nama_barang)->where('Perusahaan', $nyariperusahaan->kode_perusahaan)->first();
            $ceklagi = Barang::where('barang_id', $barang->barang_id)->where('Perusahaan', $barang->Perusahaan)->first();

            $barang = Barang::where('nama_barang', $penerimaanBarang->nama_barang)->first();

            if (!$barang) {
                return redirect()->back()->with('error', 'Barang tidak ditemukan.');
            }

            $harga_jual = $ceklagi->harga_jual;
            $jumlah_harga = $harga_jual * $request->jumlah_barang;
            $total_bayar = $jumlah_harga - ($request->diskon / 100) * $jumlah_harga;


            $penerimaanBarang->update([
                'nama_barang' => $request->nama_barang,
                'jumlah_barang' => $request->jumlah_barang,
                'total_bayar' => $total_bayar,
            ]);

            return redirect('/dataPB')->with('success', 'Data berhasil diubah');
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePenerimaanBarangRequest $request, PenerimaanBarang $penerimaanBarang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PenerimaanBarang $penerimaanBarang)
    {
        //
    }

    public function status($status, $id)
    {
        try {
            if ($status == 'Approve') {

                // // Ambil semua entri stok berdasarkan id_pb
                // $listStok = detail_pb::where('id_pb', $id)->get();

                // // Loop melalui setiap entri stok
                // foreach ($listStok as $stok) {
                //     // Tambahkan stok ke setiap barang
                //     $barang = Barang::where('barang_id', $stok->barang_id)->first();

                //     if ($barang) {
                //         $barang->stok += $stok->stok;
                //         $barang->save();
                //     }
                // }
                PenerimaanBarang::where('id_pb', $id)->update(['status' => $status]);
                return redirect('/dataPB')->with('status', 'Data berhasil di setujui');
            }
            if ($status == 'Decline') {
                PenerimaanBarang::where('id_pb', $id)->update(['status' => $status]);
                return redirect('/dataPB')->with('status', 'Data berhasil di tolak');
            } else {
                return redirect('/dataPB')->with('error', 'Terjadi kesalahan');
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function faktur($id_pb)
    {
        try {
            $latestFakturBeli = faktur_beli::latest()->first();

            // Calculate the next ID
            $nextId = $latestFakturBeli ? ($latestFakturBeli->id + 1) : 1;

            // Format the ID for the purchase order
            $idFormatted = str_pad($nextId, 4, '0', STR_PAD_LEFT);
            $FakturBeliID = 'FB-' . $idFormatted;
            $tanggalHariIni = Carbon::now()->setTimezone('Asia/Jakarta')->toDateString();

            $purchaseOrders = PurchaseOrder::where('id_pb', $id_pb)->get();
            $perusahaan = Perusahaan::all();
            $detailTotal = 0;
            $detailBarang = 0;
            $detail = []; // Initialize $detail as an empty array


            $po = PenerimaanBarang::where('id_pb', $id_pb)->first();
            foreach ($purchaseOrders as $purchaseOrder) {
                $details = detail_po::where('id_po', $purchaseOrder->id_po)->first();
                if ($details) {
                    $detaillagi = detail_po::where('id_po', $purchaseOrder->id_po)->get();
                    $detail[] = $detaillagi;
                }
            }

            $akun = BukuBesar::all();

            return view('barang.barangmasuk.faktur.fakturbeli', compact('FakturBeliID', 'id_pb', 'purchaseOrders', 'detailTotal', 'detailBarang', 'detail', 'perusahaan', 'tanggalHariIni', 'po', 'akun'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function iseng()
    {
        try {
            $purchaseOrder = PurchaseOrder::all();

            return view('ujicoba.pb', compact('purchaseOrder'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
