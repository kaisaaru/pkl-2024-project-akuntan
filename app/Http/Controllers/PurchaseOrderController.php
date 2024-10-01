<?php

namespace App\Http\Controllers;

use App\Models\detail_po;
use App\Models\PenerimaanBarang;
use App\Models\po_line;
use App\Models\PurchaseOrder;
use App\Models\Barang;
use App\Models\Termin;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\StorePurchaseOrderRequest;
use App\Http\Requests\UpdatePurchaseOrderRequest;
use App\Repository\PurchaseOrder\PurchaseOrderRepository;

class PurchaseOrderController extends Controller
{
    protected $purchaseOrderRepository;
    public function __construct(
        PurchaseOrderRepository $purchaseOrderRepository
    ) {
        $this->purchaseOrderRepository = $purchaseOrderRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $id = auth()->user()->id;
            return $this->purchaseOrderRepository->index($id);
        } catch (Exception $e) {
            return redirect('/dataPO')->with('error', 'Error retrieving purchase orders.');
        }
    }

    public function print($id)
    {
        try {
            return $this->purchaseOrderRepository->print($id);
        } catch (Exception $e) {
            return redirect('/dataPO')->with('error', 'Error retrieving purchase orders.');
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
    public function store(StorePurchaseOrderRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
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
                'nama_perusahaan' => 'required',
            ]);
            if (!$request) {
                return redirect()->back()->with('error', 'Purchase order data not found.');
            }
            return $this->purchaseOrderRepository->edit($request, $id);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        try {
            $validatedData = $request->validate([
                'kategori_barang' => 'required|unique:kategoris,kategori_barang',
            ]);
            return $this->purchaseOrderRepository->update($id, $validatedData);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        //
    }

    public function purchaseOrder()
    {
        try {
            return $this->purchaseOrderRepository->purchaseOrder();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        // Find the latest purchase order
    }

    public function CreatepurchaseOrder(Request $request)
    {
        try {
            // dd($request);
            $result = $this->purchaseOrderRepository->CreatepurchaseOrder($request);
            return $result;
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
            // return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, $id)
    {
        // dd($request->input());
        try {
            return $this->purchaseOrderRepository->updateStatus($request, $id);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        // dd($id)
    }
}
