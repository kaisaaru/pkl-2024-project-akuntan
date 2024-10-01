<?php

namespace App\Http\Controllers;

use App\Repository\Laporan\LaporanRepository;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    protected $laporanRepository;

    public function __construct(LaporanRepository $laporanRepository)
    {
        $this->laporanRepository = $laporanRepository;
    }

    public function neraca()
    {
        try {
            return $this->laporanRepository->neraca();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function labarugi(Request $request)
    {
        try {
            return $this->laporanRepository->labarugi($request);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function labarugiwithtgl(Request $request)
    {
        try {
            return $this->laporanRepository->labarugiwithtgl($request);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function pembelian()
    {
        try {
            return $this->laporanRepository->pembelian();
        } catch (\Throwable $th) {
            // Handle the exception
        }
    }

    public function printpembelian()
    {
        try {
            return $this->laporanRepository->printpembelian();
        } catch (\Throwable $th) {
            // Handle the exception
        }
    }

    public function penjualan()
    {
        try {
            return $this->laporanRepository->penjualan();
        } catch (\Throwable $th) {
            // Handle the exception
        }
    }

    public function printpenjualan()
    {
        try {
            return $this->laporanRepository->printpenjualan();
        } catch (\Throwable $th) {
            // Handle the exception
        }
    }
}
