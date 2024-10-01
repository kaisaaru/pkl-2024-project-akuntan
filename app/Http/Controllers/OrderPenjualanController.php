<?php

namespace App\Http\Controllers;

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
use App\Repository\OrderPenjualan\OrderPenjualanRepository;

class OrderPenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $orderpenjualanRepository;
    public function __construct(OrderPenjualanRepository $orderpenjualanRepository)
    {
        $this->orderpenjualanRepository = $orderpenjualanRepository;
    }
    public function index()
    {
        try {
            $id = auth()->user()->id;
            return $this->orderpenjualanRepository->index($id);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function print($id_so)
    {
        try {
            return $this->orderpenjualanRepository->print($id_so);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function laporan()
    {
        try {
            return $this->orderpenjualanRepository->laporan();
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
            return $this->orderpenjualanRepository->edit($request, $id);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
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
            return $this->orderpenjualanRepository->OrderPenjualan();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


    public function CreateOrderPenjualan(Request $request)
    {
        try {
            return $this->orderpenjualanRepository->CreateOrderPenjualan($request);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function status(Request $request, $id)
    {
        // dd($request->input());
        try {
            return $this->orderpenjualanRepository->updateStatus($request, $id);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        // dd($id)
    }
}
