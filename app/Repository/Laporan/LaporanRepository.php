<?php

namespace App\Repository\Laporan;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BukuBesar;
use App\Models\Neraca;
use App\Models\SubBukuBesar;
use App\Models\faktur_beli;
use App\Models\faktur_jual;
use App\Models\TipeAkun;
use App\Models\Perusahaan;
use App\Models\RiwayatBukuBesar;
use Carbon\Carbon;

class LaporanRepository
{
    public function neraca()
    {
        try {
            $neraca = Neraca::all();
            $aktivaData = TipeAkun::where('jenis', 'Aset')->with(['bukuBesar', 'bukuBesar.subBukuBesar'])->get();
            $pasivaData = TipeAkun::where('jenis', 'Kewajiban Dan Ekuitas')->with(['bukuBesar', 'bukuBesar.subBukuBesar'])->get();

            // return view('laporan.neraca', compact('aktivaData', 'pasivaData'));
            return view('laporan.neraca', compact('aktivaData', 'pasivaData', 'neraca'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function labarugi(Request $request)
    {
        try {
            $awal = Carbon::parse($request->input('awal'));
            $akhir = Carbon::parse($request->input('akhir'));

            if ($awal && $akhir) {
                $penjualan = BukuBesar::where('ket', 'Penjualan')->with(['subBukuBesar'])->get();
                $hpp = BukuBesar::where('ket', 'Harga Pokok Penjualan')->with(['subBukuBesar'])->get();
                // dump($penjualan);
                $totalpenjualan = RiwayatBukuBesar::where('no_bukubesar', 112)
                    ->whereBetween('tanggal', [$awal, $akhir])
                    ->sum('kredit');

                $pbdawal = RiwayatBukuBesar::where('no_bukubesar', 103)
                    ->where('tanggal', '<', $awal)
                    ->orderBy('tanggal', 'desc') // Urutkan berdasarkan tanggal secara descending
                    ->first();

                $saldo_kumulatif_pbdawal = $pbdawal ? $pbdawal->saldo_kumulatif : 0;

                $pembelian = RiwayatBukuBesar::where('no_bukubesar', 103)
                    ->whereBetween('tanggal', [$awal, $akhir])
                    ->sum('debet');

                $pbdakhir = RiwayatBukuBesar::where('no_bukubesar', 103)
                    ->where('tanggal', '<=', $akhir)
                    ->whereBetween('tanggal', [$awal, $akhir])
                    ->orderBy('tanggal', 'desc')
                    ->first();

                // dd($totalpenjualan);
                $saldo_kumulatif_pbdakhir = $pbdakhir ? $pbdakhir->saldo_kumulatif : 0;
                // dump($pbdakhir);

                $neraca = Neraca::all();
                $penjualan = BukuBesar::where('ket', 'Penjualan')->with(['subBukuBesar'])->get();
                $hpp = BukuBesar::where('ket', 'Harga Pokok Penjualan')->with(['subBukuBesar'])->get();
                $biayalain = BukuBesar::where('tipe', 'Biaya Lain')->with(['subBukuBesar'])->get();
                $lababersih = TipeAkun::where('tipe', 'Laba Bersih')->with(['bukuBesar', 'bukuBesar.subBukuBesar'])->get();

                return view('laporan.labarugi', compact(
                    'neraca',
                    'penjualan',
                    'hpp',
                    'lababersih',
                    'biayalain',
                    'pbdawal',
                    'totalpenjualan',
                    'saldo_kumulatif_pbdawal',
                    'pembelian',
                    'saldo_kumulatif_pbdakhir'
                ));

                // return view('laporan.labarugiwithtgl', compact('penjualan', 'hpp', 'penjualan', 'pbdawal', 'saldo_kumulatif_pbdawal', 'pembelian', 'pbdakhir', 'saldo_kumulatif_pbdakhir'));
            } else {
                $neraca = Neraca::all();
                $penjualan = BukuBesar::where('ket', 'Penjualan')->with(['subBukuBesar'])->get();
                $hpp = BukuBesar::where('ket', 'Harga Pokok Penjualan')->with(['subBukuBesar'])->get();
                $biayalain = BukuBesar::where('tipe', 'Biaya Lain')->with(['subBukuBesar'])->get();
                $lababersih = TipeAkun::where('tipe', 'Laba Bersih')->with(['bukuBesar', 'bukuBesar.subBukuBesar'])->get();

                return view('laporan.labarugi', compact('neraca', 'penjualan', 'hpp', 'lababersih', 'biayalain', 'awal', 'akhir'));
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function labarugiwithtgl(Request $request)
    {
        try {
            $awal = Carbon::parse($request->input('awal'));
            $akhir = Carbon::parse($request->input('akhir'));

            if ($awal && $akhir) {
                $penjualan = BukuBesar::where('ket', 'Penjualan')->with(['subBukuBesar'])->get();
                $hpp = BukuBesar::where('ket', 'Harga Pokok Penjualan')->with(['subBukuBesar'])->get();
                // dump($penjualan);
                $totalpenjualan = RiwayatBukuBesar::where('ket_subbukubesar', "Penjualan")
                    ->whereBetween('tanggal', [$awal, $akhir])
                    ->sum('kredit');

                $pbdawal = RiwayatBukuBesar::where('ket_subbukubesar', "Persediaan Barang Dagang")
                    ->where('tanggal', '<', $awal)
                    ->orderBy('tanggal', 'desc') // Urutkan berdasarkan tanggal secara descending
                    ->first();

                $saldo_kumulatif_pbdawal = $pbdawal ? $pbdawal->saldo_kumulatif : 0;

                $pembelian = RiwayatBukuBesar::where('ket_subbukubesar', "Persediaan Barang Dagang")
                    ->whereBetween('tanggal', [$awal, $akhir])
                    ->sum('debet');

                $pbdakhir = RiwayatBukuBesar::where('ket_subbukubesar', "Persediaan Barang Dagang")
                    ->where('tanggal', '<=', $akhir)
                    ->whereBetween('tanggal', [$awal, $akhir])
                    ->orderBy('tanggal', 'desc')
                    ->first();

                // dd($pbdakhir);
                // dd($totalpenjualan);
                $saldo_kumulatif_pbdakhir = $pbdakhir ? $pbdakhir->saldo_kumulatif : 0;

                $neraca = Neraca::all();
                $penjualan = BukuBesar::where('ket', 'Penjualan')->with(['subBukuBesar'])->get();
                $hpp = BukuBesar::where('ket', 'Harga Pokok Penjualan')->with(['subBukuBesar'])->get();

                $bbm = RiwayatBukuBesar::where('ket_subbukubesar', "Biaya BBM")
                    ->where('tanggal', '<=', $akhir)
                    ->whereBetween('tanggal', [$awal, $akhir])
                    ->orderBy('tanggal', 'desc')
                    ->first();

                $saldo_kumulatif_bbm = $bbm ? $bbm->saldo_kumulatif : 0;

                $listrik = RiwayatBukuBesar::where('ket_subbukubesar', "Biaya Listrik")
                    ->where('tanggal', '<=', $akhir)
                    ->whereBetween('tanggal', [$awal, $akhir])
                    ->orderBy('tanggal', 'desc')
                    ->first();

                $saldo_kumulatif_listrik = $listrik ? $listrik->saldo_kumulatif : 0;

                $pulsa_telepon = RiwayatBukuBesar::where('ket_subbukubesar', "Biaya Pulsa Telepon")
                    ->where('tanggal', '<=', $akhir)
                    ->whereBetween('tanggal', [$awal, $akhir])
                    ->orderBy('tanggal', 'desc')
                    ->first();

                $saldo_kumulatif_pulsa = $pulsa_telepon ? $pulsa_telepon->saldo_kumulatif : 0;

                $pengiriman = RiwayatBukuBesar::where('ket_subbukubesar', "Biaya Pengiriman")
                    ->where('tanggal', '<=', $akhir)
                    ->whereBetween('tanggal', [$awal, $akhir])
                    ->orderBy('tanggal', 'desc')
                    ->first();

                $saldo_kumulatif_pengiriman = $pengiriman ? $pengiriman->saldo_kumulatif : 0;

                $gajikaryawan = RiwayatBukuBesar::where('ket_subbukubesar', "Biaya Gaji Karyawan")
                    ->where('tanggal', '<=', $akhir)
                    ->whereBetween('tanggal', [$awal, $akhir])
                    ->orderBy('tanggal', 'desc')
                    ->first();

                $saldo_kumulatif_gaji = $gajikaryawan ? $gajikaryawan->saldo_kumulatif : 0;

                $biayalain = BukuBesar::where('tipe', 'Biaya Lain')->with(['subBukuBesar'])->get();
                $lababersih = TipeAkun::where('tipe', 'Laba Bersih')->with(['bukuBesar', 'bukuBesar.subBukuBesar'])->get();

                return view('laporan.labarugiwithtgl', compact(
                    'neraca',
                    'penjualan',
                    'hpp',
                    'lababersih',
                    'biayalain',
                    'pbdawal',
                    'totalpenjualan',
                    'saldo_kumulatif_pbdawal',
                    'pembelian',
                    'saldo_kumulatif_pbdakhir',
                    'awal',
                    'akhir',
                    'saldo_kumulatif_bbm',
                    'saldo_kumulatif_listrik',
                    'saldo_kumulatif_pulsa',
                    'saldo_kumulatif_pengiriman',
                    'saldo_kumulatif_gaji',
                ));

                // return view('laporan.labarugiwithtgl', compact('penjualan', 'hpp', 'penjualan', 'pbdawal', 'saldo_kumulatif_pbdawal', 'pembelian', 'pbdakhir', 'saldo_kumulatif_pbdakhir'));
            } else {
                $neraca = Neraca::all();
                $penjualan = BukuBesar::where('ket', 'Penjualan')->with(['subBukuBesar'])->get();
                $hpp = BukuBesar::where('ket', 'Harga Pokok Penjualan')->with(['subBukuBesar'])->get();
                $biayalain = BukuBesar::where('tipe', 'Biaya Lain')->with(['subBukuBesar'])->get();
                $lababersih = TipeAkun::where('tipe', 'Laba Bersih')->with(['bukuBesar', 'bukuBesar.subBukuBesar'])->get();

                return view('laporan.labarugiwithtgl', compact('neraca', 'penjualan', 'hpp', 'lababersih', 'biayalain',));
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function pembelian()
    {
        try {
            $faktur = faktur_beli::all();
            return view('laporan.pembelian.pembelian', compact('faktur'));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function printpembelian()
    {
        try {
            $faktur = faktur_beli::all();
            $perusahaan = Perusahaan::where('jenis', 'Supplier')->get();
            return view('laporan.pembelian.print', compact('faktur', 'perusahaan'));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function penjualan()
    {
        try {
            $faktur = faktur_jual::all();
            return view('laporan.penjualan.penjualan', compact('faktur'));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function printpenjualan()
    {
        try {
            $faktur = faktur_jual::all();
            $perusahaan = Perusahaan::where('jenis', 'Konsumen')->get();
            return view('laporan.penjualan.print', compact('faktur', 'perusahaan'));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
