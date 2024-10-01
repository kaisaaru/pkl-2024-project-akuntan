<?php

namespace App\Repository\PurchaseOrder;

use App\Models\Barang;
use App\Models\detail_po;
use App\Models\Perusahaan;
use App\Models\po_line;
use App\Models\PurchaseOrder;
use App\Models\Termin;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderRepository
{
    public function index($id)
    {
        if ($id === null) {
            return redirect('/dataPO')->with('error', 'Terjadi Kesalahan');
        }
        try {
            $purchaseOrders = PurchaseOrder::paginate(20);
            $perusahaan = Perusahaan::all();
            $detailTotal = 0;
            $detailBarang = 0;
            $detail = []; // Initialize $detail as an empty array

            foreach ($purchaseOrders as $purchaseOrder) {
                $details = detail_po::where('id_po', $purchaseOrder->id_po)->first();
                if ($details) {
                    $detaillagi = detail_po::where('id_po', $purchaseOrder->id_po)->get();
                    $detail[] = $detaillagi;
                }
            }

            return view('barang.barangmasuk.po.dataPO', compact('purchaseOrders', 'detailTotal', 'detailBarang', 'detail', 'perusahaan'));
        } catch (Exception $e) {
            return redirect('/dataPO')->with('error', 'Error retrieving purchase orders.');
        }
    }
    public function updateStatus(Request $request, $id)
    {
        try {
            $status = $request->get('status');
            // dd($status);
            if ($status == 'Approve' || $status == 'Decline') {
                PurchaseOrder::where('id_po', $id)->update(['status' => $status]);
                return array(
                    'status' => 'success',
                    'message' => 'successfully update status'
                );
            } else {
                return array(
                    'status' => 'error',
                    'message' => 'Internal server rusak ' + $status
                );
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function print($id)
    {
        try {
            $user = Auth::user();
            $purchaseOrders = PurchaseOrder::where('id_po', $id)->first();
            $supplier = $purchaseOrders->nama_perusahaan;
            $perusahaan = Perusahaan::where('nama_perusahaan', $supplier)->first();
            $perusahaankita = Perusahaan::where('kode_perusahaan', $user->kode_perusahaan)->first();
            $detailTotal = 0;
            $detailBarang = 0;
            $hargaAfterDiskon = 0;
            $totalHargaSemua = 0;
            $detail = []; // Initialize $detail as an empty array
            $barang = []; // Initialize $barang as an empty array
            $kodeBarangArray = [];

            $details = detail_po::where('id_po', $purchaseOrders->id_po)->with('barang')->latest()->first();
            if ($details) {
                $detaillagi = detail_po::where('id_po', $purchaseOrders->id_po)->get();
                $detail[] = $detaillagi;
                $kodeBarangArray = $details->barang->kode_barang;
                foreach ($detaillagi as $detailItem) {
                    $barang = Barang::where('barang_id', $detailItem->barang_id)->first();
                }
            }

            return view('barang.barangmasuk.po.print', compact('purchaseOrders', 'detailTotal', 'detailBarang', 'detail', 'perusahaan', 'totalHargaSemua',  'barang', 'kodeBarangArray', 'perusahaan', 'perusahaankita'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
            // return redirect('/dataPO')->with('error', 'Error printing purchase order.');
        }
    }
    public function edit(Request $request, $id)
    {
        try {

            $purchaseOrder = PurchaseOrder::where('id_po', $id)->first();

            if (!$purchaseOrder) {
                return redirect()->back()->with('error', 'Purchase order data not found.');
            }

            $nyariperusahaan = Perusahaan::where('kode_perusahaan', $purchaseOrder->nama_perusahaan)->first();
            $barang = Barang::where('nama_barang', $purchaseOrder->nama_barang)->where('Perusahaan', $nyariperusahaan->kode_perusahaan)->first();
            $ceklagi = Barang::where('barang_id', $barang->barang_id)->where('Perusahaan', $barang->Perusahaan)->first();

            if (!$barang) {
                return redirect()->back()->with('error', 'Barang tidak ditemukan.');
            }

            // $harga_jual = $ceklagi->harga_jual;
            // $jumlah_harga = $harga_jual * $request->jumlah_barang;
            // $total_bayar = $jumlah_harga - ($request->diskon / 100) * $jumlah_harga;


            $purchaseOrder->update([
                'tanggal_po' => $request->nama_barang,
                'Perusahaan' => $request->nama_perusahaan,
            ]);

            return redirect('/dataPO')->with('success', 'Data berhasil diubah');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan.');
        }
    }

    public function update($id, $validatedData)
    {
        try {
            $data = detail_po::find($id);

            // Validasi data yang dikirim dari form

            $data->update($validatedData);

            $kategori = $data->kategori_barang;

            return redirect('/kategori')->with('update', 'Kategori Barang <strong>' . $kategori . '</strong> berhasil diupdate.');
            //
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function purchaseOrder()
    {
        try {
            $latestPurchaseOrder = PurchaseOrder::latest()->first();

            // Calculate the next ID
            $nextId = $latestPurchaseOrder ? ($latestPurchaseOrder->id + 1) : 1;

            // Format the ID for the purchase order
            $idFormatted = str_pad($nextId, 4, '0', STR_PAD_LEFT);
            $purchaseOrderId = 'PO-' . $idFormatted;
            $termin = Termin::all();

            $tanggalHariIni = Carbon::now()->setTimezone('Asia/Jakarta')->toDateString();
            $barang = Barang::all();
            $Perusahaan = Perusahaan::all();
            $termin = Termin::all();
            // Build a list of Perusahaan options
            $PerusahaanOptions = Barang::select('barangs.Perusahaan as barang_id', 'barangs.nama_barang', 'perusahaans.kode_perusahaan as Perusahaan_id', 'perusahaans.nama_perusahaan as Perusahaan_nama')
                ->leftJoin('perusahaans', 'barangs.Perusahaan', '=', 'perusahaans.kode_perusahaan')
                ->get();

            return view('barang.barangmasuk.po.purchaseorder', compact('barang', 'Perusahaan', 'PerusahaanOptions', 'purchaseOrderId', 'tanggalHariIni', 'termin'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        // Find the latest purchase order

    }

    public function CreatepurchaseOrder(Request $request)
    {
        try {
            $selectedItemsArray = $request->input('selectedItems');
            $selectedItemsArrayArray = json_decode($selectedItemsArray, true);
            // dd($selectedItemsArrayArray);
            $id = auth()->user();

            $request->validate([
                'tanggal_po' => 'required',
                'kode_perusahaan' => 'required',
            ]);


            $termin = $request->input('termin');
            $jatuh_tempo = $request->input('jatuh_tempo');
            $tanggal_termin = $request->input('tanggal_termin');
            // dd($tanggal_termin);
            $poline = po_line::create([
                'user_id' => $id->id,
                'hariini' => now()->toDateString(),
            ]);
            $id_po = $poline->id_po;

            //fix

            $pusinglo = Perusahaan::where('kode_perusahaan', $request->kode_perusahaan)->first();

            if ($termin && $jatuh_tempo) {
                return redirect()->back()->with('error', 'Jatuh tempo harap tidak diisi dua-duanya');
            } else {
                if (!$termin && !$jatuh_tempo) {
                    return redirect()->back()->with('error', 'Jatuh tempo diisi salah satunya yang sudah ada atau buat baru ');
                }
            }

            if ($termin || $jatuh_tempo) {
                if ($jatuh_tempo) {
                    Termin::create([
                        'jatuh_tempo' => $jatuh_tempo,
                        'tanggal_termin' => $tanggal_termin,
                    ]);
                }
                if ($termin) {
                    $tanggal_termin = $termin ? Carbon::parse($request->input('tanggal_po'))->addDays(Termin::where('kode_termin', $termin)->first()->jatuh_tempo)->toDateString() : $jatuh_tempo = $request->input('jatuh_tempo');
                }
            }
            // $tgltermin = Termin::where('kode_termin', $termin)->first()->toDateString();
            // dd($tgltermin);
            // $poline = po_line::latest()->first();

            $purchaseOrder = PurchaseOrder::create([
                'id_po' => $poline->id_po,
                'user' => $id->name,
                'tanggal_po' => $request->tanggal_po,
                'kode_perusahaan' => $pusinglo->kode_perusahaan,
                'nama_perusahaan' => $pusinglo->nama_perusahaan,
                'detail_po' => $poline->id_detailpo,
                'jatuh_tempo' => $tanggal_termin,
            ]);

            if ($purchaseOrder && $poline->id_po !== null) {
                foreach ($selectedItemsArrayArray as $selectedItem) {
                    $satuan = Barang::where('barang_id', $selectedItem['barang_id'])->first();
                    detail_po::create([
                        'id_po' => $poline->id_po, // Provide the id_po value here
                        'id_detail_po' => $poline->id_detailpo,
                        'barang_id' => $selectedItem['barang_id'],
                        'nama_barang' => $selectedItem['nama_barang'],
                        'satuan' => $satuan->satuan,
                        'stok' => $selectedItem['quantity'],
                        'harga' => $selectedItem['price'],
                        'potongan' => $selectedItem['discount'] ?? 0,
                        'diskon' => $selectedItem['discountpersen'] ?? 0,
                        'total_harga' => $selectedItem['total'],
                    ]);
                }
            } else {
                return back()->with('error', 'Failed to add data');
            }
            $pb = session('pb');
            $timeDiff = session('timeDiff');

            return redirect('/dataPO')->with('success', 'PO <strong>' . $id_po . '</strong> berhasil ditambah');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function CreatepurchaseOrderSeeder($validatedData)
    {
        // dd($validatedData);
        try {
            $selectedItemsArray = $validatedData['selectedItems'];
            $selectedItemsArrayArray = json_decode($selectedItemsArray, true);
            // dd($selectedItemsArrayArray);
            $id = auth()->user();
            if ($id === null) {
                $id = $validatedData['id'];
            }
            // dd($id);
            $termin = $validatedData['termin'];
            $jatuh_tempo = $validatedData['jatuh_tempo'];
            $tanggal_termin = $validatedData['tanggal_termin'];
            // dd($tanggal_termin);
            $poline = po_line::create([
                'user_id' => $id,
                'hariini' => now()->toDateString(),
            ]);
            $id_po = $poline->id_po;

            //fix

            $pusinglo = Perusahaan::where('kode_perusahaan', $validatedData['kode_perusahaan'])->first();


            if ($termin || $jatuh_tempo) {
                if ($jatuh_tempo) {
                    Termin::create([
                        'jatuh_tempo' => $jatuh_tempo,
                        'tanggal_termin' => $tanggal_termin,
                    ]);
                }
                if ($termin) {
                    $tanggal_termin = $termin ? Carbon::parse($validatedData['tanggal_po'])->addDays(Termin::where('kode_termin', $termin)->first()->jatuh_tempo)->toDateString() : $jatuh_tempo = $validatedData['jatuh_tempo'];
                }
            }
            // $tgltermin = Termin::where('kode_termin', $termin)->first()->toDateString();
            // dd($tgltermin);
            // $poline = po_line::latest()->first();

            $purchaseOrder = PurchaseOrder::create([
                'id_po' => $poline->id_po,
                'user' => $validatedData['name'],
                'tanggal_po' => $validatedData['tanggal_po'],
                'kode_perusahaan' => $pusinglo->kode_perusahaan,
                'nama_perusahaan' => $pusinglo->nama_perusahaan,
                'detail_po' => $poline->id_detailpo,
                'jatuh_tempo' => $tanggal_termin,
            ]);

            foreach ($selectedItemsArrayArray as $selectedItem) {
                $satuan = Barang::where('barang_id', $selectedItem['barang_id'])->first();
                detail_po::create([
                    'id_po' => $poline->id_po, // Provide the id_po value here                    
                    'barang_id' => $selectedItem['barang_id'],
                    'nama_barang' => $selectedItem['nama_barang'],
                    'satuan' => $satuan->satuan,
                    'stok' => $selectedItem['quantity'],
                    'harga' => $selectedItem['price'],
                    'potongan' => $selectedItem['discount'] ?? 0,
                    'diskon' => $selectedItem['discountpersen'] ?? 0,
                    'total_harga' => $selectedItem['total'],
                ]);
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
