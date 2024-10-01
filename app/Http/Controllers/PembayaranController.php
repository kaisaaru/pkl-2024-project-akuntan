<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorepembayaranRequest;
use App\Http\Requests\UpdatepembayaranRequest;
use App\Models\BukuBesar;
use App\Models\detail_fj;
use App\Models\pembayaran;
use App\Models\Perusahaan;
use App\Repository\Pembayaran\PembayaranRepository;
use Exception;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    protected $pembayaranRepository;
    public function __construct(PembayaranRepository $pembayaranRepository)
    {
        $this->pembayaranRepository = $pembayaranRepository;
    }

    public function dataPayment()
    {
        $user = auth()->user()->id;
        $result = $this->pembayaranRepository->dataPayment($user);
        if (is_array($result) && array_key_exists('message', $result)) {
            return  redirect()->back()->with(['error' => $result['message']]);
        }

        return $result;
    }
    public function index()
    {
        $user = auth()->user()->id;
        $result = $this->pembayaranRepository->index($user);
        if (is_array($result) && array_key_exists('message', $result)) {
            return  redirect()->back()->with(['error' => $result['message']]);
        }

        return $result;
    }

    public function indextahap2($id_bayar, $id)
    {
        $result = $this->pembayaranRepository->bayartahap2($id_bayar, $id);
        if (is_array($result) && array_key_exists('message', $result)) {
            return  redirect()->back()->with(['error' => $result['message']]);
        }

        return $result;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createtahap1(Request $request, $id, $id_bayar)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'id_bayar' => 'required',
            'paymentdate' => 'required',
            'no_akun' => 'required',
            'no_konsumen' => 'required',
            "nilaifaktur.*" => 'required',
            "sisanilaiawal.*" => 'required',
            "cb.*" => 'required',
            "jumlahnilai.*" => 'required',
            "sisa.*" => 'required'
        ]);


        // dd($id);
        try {
            if ($validatedData) {
                $result = $this->pembayaranRepository->bayartahap1($request, $id, $id_bayar);
                if (is_array($result) && array_key_exists('message', $result)) {
                    return  redirect()->back()->with(['error' => 'Terjadi kesalahan']);
                }

                return $result;
            }
        } catch (Exception $e) {
            return  redirect()->back()->with('error', $e);
        }
    }

    public function createtahap2(Request $request, $id, $id_bayar)
    {
        // $validatedData = $request->validate([
        //     'autoakun.*' => 'required',           
        //     'kredit.*' => 'required',
        //     'debet.*' => 'required',
        //     'ket.*' => 'required',            
        //     'mu.*' => 'required',
        //     'kurs.*' => 'required',
        //     'jumlah.*' => 'required',          
        //     'akunpembantu.*' => 'required',
        //     // aturan validasi tambahan untuk setiap elemen dalam array                                            
        // ]);
        // dd($request);





        // dd($id);
        try {           
                $result = $this->pembayaranRepository->bayartahap2create($request, $id, $id_bayar);                
                return $result;           
        } catch (Exception $e) {
            return  redirect()->back()->with('error', $e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorepembayaranRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(pembayaran $pembayaran)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(pembayaran $pembayaran)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatepembayaranRequest $request, pembayaran $pembayaran)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(pembayaran $pembayaran)
    {
        //
    }
}
