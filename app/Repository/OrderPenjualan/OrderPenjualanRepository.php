<?php

namespace App\Repository\OrderPenjualan;

use App\Models\detail_op;
use App\Models\opline;
use App\Models\SuratJalan;
use App\Models\OrderPenjualan;
use App\Models\Barang;
use App\Models\Perusahaan;
use Exception;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Termin;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\StoreOrderPenjualanRequest;
use App\Http\Requests\UpdateOrderPenjualanRequest;

class OrderPenjualanRepository
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        if ($id === null) {
            return redirect('/dataOP')->with('error', 'Terjadi Kesalahan');
        }
        try {
            $OrderPenjualan = OrderPenjualan::paginate(20);
            $nama_perusahaans = '-';
            foreach ($OrderPenjualan as $order) {
                $nama_perusahaan = Perusahaan::where('kode_perusahaan', $order->kode_perusahaan)->first();
                $nama_perusahaans = $nama_perusahaan->nama_perusahaan;
            }
            $Perusahaan = Perusahaan::all();
            $detailTotal = 0;
            $detailBarang = 0;
            $detail = []; // Initialize $detail as an empty array

            foreach ($OrderPenjualan as $s) {
                $details = detail_op::where('id_so', $s->id_so)->first();
                if ($details) {
                    $detaillagi = detail_op::where('id_so', $s->id_so)->get();
                    $detail[] = $detaillagi;
                }
            }

            return view('barang.barangkeluar.op.dataOP', compact('OrderPenjualan', 'detailTotal', 'detailBarang', 'detail', 'Perusahaan'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function print($id_so)
    {
        try {
            $user = Auth::user();
            $OrderPenjualan = OrderPenjualan::where('id_so', $id_so)
                ->orderBy('id_so') // Menambahkan ini untuk mengurutkan berdasarkan barang_id
                ->first();
            $supplier = $OrderPenjualan->kode_perusahaan;
            $perusahaan = Perusahaan::where('kode_perusahaan', $supplier)->first();
            $perusahaankita = Perusahaan::where('kode_perusahaan', $user->kode_perusahaan)->first();

            $detailTotal = 0;
            $detailBarang = 0;
            $hargaAfterDiskon = 0;
            $totalHargaSemua = 0;
            $detail = []; // Initialize $detail as an empty array
            $barang = []; // Initialize $detail as an empty array

            $details = detail_op::where('id_so', $OrderPenjualan->id_so)->with('barang')->first();
            if ($details) {
                $detaillagi = detail_op::where('id_so', $OrderPenjualan->id_so)->get();
                $detail[] = $detaillagi;
                foreach ($detaillagi as $detailItem) {
                    $barang = Barang::where('barang_id', $detailItem->barang_id)->get();
                }
            }

            if ($OrderPenjualan) {
                return view('barang.barangkeluar.op.print2', compact('OrderPenjualan', 'detailTotal', 'detailBarang', 'detail', 'perusahaan', 'totalHargaSemua', 'perusahaan', 'barang'));
            } else {
                // Handle ketika data tidak ditemukan
                return redirect('/halaman_error')->with('error', 'Purchase Order tidak ditemukan');
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function laporan()
    {
        try {
            $OrderPenjualan = OrderPenjualan::all();
            return view('barang.barangkeluar.op.laporan', compact('OrderPenjualan'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // You can add implementation here if needed
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderPenjualanRequest $request)
    {
        // You can add implementation here if needed
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderPenjualan $OrderPenjualan)
    {
        // You can add implementation here if needed
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'nama_barang' => 'required',
                'nama_perusahaan' => 'required',
            ]);

            $orderPenjualan = OrderPenjualan::where('id_so', $id)->first();

            if (!$orderPenjualan) {
                return redirect()->back()->with('error', 'Data order penjualan tidak ditemukan.');
            }

            $nyariperusahaan = Perusahaan::where('kode_perusahaan', $orderPenjualan->nama_perusahaan)->first();
            $barang = Barang::where('nama_barang', $orderPenjualan->nama_barang)->where('Perusahaan', $nyariperusahaan->kode_perusahaan)->first();
            $ceklagi = Barang::where('barang_id', $barang->barang_id)->where('Perusahaan', $barang->Perusahaan)->first();

            $barang = Barang::where('nama_barang', $orderPenjualan->nama_barang)->first();

            if (!$barang) {
                return redirect()->back()->with('error', 'Barang tidak ditemukan.');
            }

            // $harga_jual = $ceklagi->harga_jual;
            // $jumlah_harga = $harga_jual * $request->jumlah_barang;
            // $total_bayar = $jumlah_harga - ($request->diskon / 100) * $jumlah_harga;

            $orderPenjualan->update([
                'tanggal_op' => $request->nama_barang,
                'Perusahaan' => $request->nama_perusahaan,
            ]);

            return redirect('/dataOP')->with('success', 'Data berhasil diubah');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderPenjualanRequest $request, OrderPenjualan $OrderPenjualan)
    {
        // You can add implementation here if needed
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderPenjualan $OrderPenjualan)
    {
        // You can add implementation here if needed
    }

    public function OrderPenjualan()
    {
        try {
            $latestOrderPenjualan = OrderPenjualan::latest()->first();

            // Calculate the next ID
            $nextId = $latestOrderPenjualan ? ($latestOrderPenjualan->id + 1) : 1;

            // Format the ID for the purchase order
            $idFormatted = str_pad($nextId, 4, '0', STR_PAD_LEFT);
            $OrderPenjualanId = 'SO-' . $idFormatted;

            $tanggalHariIni = Carbon::now()->setTimezone('Asia/Jakarta')->toDateString();
            $barang = Barang::all();
            $Perusahaan = Perusahaan::all();
            $termin = Termin::all();

            $PerusahaanOptions = Barang::select('barangs.Perusahaan as barang_id', 'barangs.nama_barang', 'perusahaans.kode_perusahaan as Perusahaan_id', 'perusahaans.nama_perusahaan as Perusahaan_nama')
                ->leftJoin('perusahaans', 'barangs.Perusahaan', '=', 'perusahaans.kode_perusahaan')
                ->get();
                
            return view('barang.barangkeluar.op.orderpenjualan', compact('barang', 'Perusahaan', 'PerusahaanOptions', 'OrderPenjualanId', 'tanggalHariIni', 'termin'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


    public function CreateOrderPenjualan(Request $request)
    {
        try {
            $selectedItemsArray = $request->input('selectedItems');
            $selectedItemsArrayArray = json_decode($selectedItemsArray, true);
            // dd($selectedItemsArrayArray);
            $id = auth()->user();

            $request->validate([
                'tanggal_op' => 'required',
                'kode_perusahaan' => 'required',
            ]);

            $termin = $request->input('termin');
            $jatuh_tempo = $request->input('jatuh_tempo');
            $tanggal_termin = $request->input('tanggal_termin');
            // dd($tanggal_termin);
            $opline = opline::create([
                'user_id' => $id->id,
                'hariini' => now()->toDateString(),
            ]);
            $id_so = $opline->id_so;

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
                    $tanggal_termin = $termin ? Carbon::parse($request->input('tanggal_op'))->addDays(Termin::where('kode_termin', $termin)->first()->jatuh_tempo)->toDateString() : $jatuh_tempo = $request->input('jatuh_tempo');
                }
            }

            // $jatuh_tempo = $termin ? Carbon::parse($request->input('tanggal_op'))->addDays(Termin::where('kode_termin', $termin)->first()->jatuh_tempo)->toDateString() : Carbon::parse($request->input('tanggal_op'))->addDays($jatuh_tempo)->toDateString();            
            // $tgltermin = Termin::where('kode_termin', $termin)->first()->toDateString();
            // dd($tgltermin);
            // $opline = opline::latest()->first();
            $orderPenjualan = OrderPenjualan::create([
                'id_so' => $opline->id_so,
                'user' => $id->name,
                'tanggal_op' => $request->tanggal_op,
                'kode_perusahaan' => $request->kode_perusahaan,
                'nama_perusahaan' => $pusinglo->nama_perusahaan,
                'detail_op' => $opline->id_detailso,
                'jatuh_tempo' => $tanggal_termin,
            ]);

            if ($orderPenjualan && $opline->id_so !== null) {
                foreach ($selectedItemsArrayArray as $selectedItem) {
                    $satuan = Barang::where('barang_id', $selectedItem['barang_id'])->first();
                    $harga_beli = Barang::where('barang_id', $selectedItem['barang_id'])->first();
                    detail_op::create([
                        'id_so' => $opline->id_so, // Provide the id_so value here
                        'id_detailso' => $opline->id_detailso,
                        'barang_id' => $selectedItem['barang_id'],
                        'nama_barang' => $selectedItem['nama_barang'],
                        'satuan' => $satuan->satuan,
                        'stok' => $selectedItem['quantity'],
                        'harga' => $selectedItem['price'],
                        'potongan' => $selectedItem['discount'] ?? 0,
                        'diskon' => $selectedItem['discountpersen'] ?? 0,
                        'total_harga' => $selectedItem['total'],
                        'harga_beli' => $harga_beli->harga_beli
                    ]);
                }
            } else {
                return back()->with('error', 'Failed to add data');
            }

            $pb = session('pb');
            $timeDiff = session('timeDiff');

            return redirect('/dataOP')->with('success', 'SO <strong>' . $id_so . '</strong> berhasil ditambah');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function updateStatus(Request $request, $id)
    {
        try {
            $status = $request->get('status');
            // dd($status);
            if ($status == 'Approve' || $status == 'Decline') {
                OrderPenjualan::where('id_so', $id)->update(['status' => $status]);
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
}
