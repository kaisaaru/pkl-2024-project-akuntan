<?php

namespace App\Repository\Pembayaran;

use App\Models\autojurnal;
use App\Models\BukuBesar;
use App\Models\detail_faktur;
use App\Models\detail_fj;
use App\Models\detail_pembayaran;
use App\Models\faktur_beli;
use App\Models\faktur_jual;
use App\Models\pembayaran;
use App\Models\PenerimaanBarang;
use App\Models\Perusahaan;
use App\Models\SubBukuBesar;
use App\Models\SuratJalan;
use App\Models\Termin;
use App\Models\triad_pembayaran;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranRepository
{
    public function dataPayment($id)
    {
        if ($id === null) {
            return redirect('/')->with('error', 'Terjadi Kesalahan');
        }
        try {
            $pembayarans = triad_pembayaran::paginate(20);
            $detailp = []; // Initialize $detailp as an empty array
            $detaila = [];

            foreach ($pembayarans as $pembayaran) {
                $detail_pembayaran = detail_pembayaran::where('id_bayar', $pembayaran->id_bayar)->first();
                $autojurnal = pembayaran::where('id_bayar', $pembayaran->id_bayar)->first();
                if ($detail_pembayaran) {
                    $detaillagip = detail_pembayaran::where('id_bayar', $pembayaran->id_bayar)->get();
                    $detailp[] = $detaillagip; // Store details using id_bayar as key
                }
                if ($autojurnal) {
                    $detaillagia = pembayaran::where('id_bayar', $pembayaran->id_bayar)->get();
                    $detaila[] = $detaillagia; // Store details using id_bayar as key
                }
            }            

            return view('barang.pembayaran.datapayment', compact('pembayarans', 'detaila', 'detailp'));
        } catch (Exception $e) {
            return redirect('/dataPayment')->with('error', $e->getMessage()); // Passing error message
        }
    }

    public function index($user)
    {
        try {
            $autojurnal = autojurnal::where('user_id', $user)->first();
            if ($autojurnal === null) {
                return redirect('/setting')->with('warning', 'Silahkan setting akun terlebih dahulu');
            } else {
                // dd($validator);
                // $validators = $validator ? 
                $validator = triad_pembayaran::where('user_id', $user)->where('status_autojurnal', 'empty')->get();
                if ($validator->isNotEmpty()) {
                    foreach ($validator as $detail) {
                        $id_bayar = $detail->id_bayar;
                    }
                    // dd($id_bayar);
                    return redirect('/autojurnal/' . $id_bayar . '/' . $user)->with('warning', 'Silahkan selesaikan pembayaran');
                }
                if ($user != null) {
                    // Generate payment ID
                    $latestPayment = Pembayaran::latest()->first();
                    $nextId = $latestPayment ? ($latestPayment->id + 1) : 1;
                    $idFormatted = str_pad($nextId, 4, '0', STR_PAD_LEFT);
                    $id_bayar = 'Pay-' . $idFormatted;

                    // Get current date
                    $tanggalHariIni = now()->setTimezone('Asia/Jakarta')->toDateString();

                    // Fetch data
                    $faktur = detail_fj::join('surat_jalans', function ($join) {
                        $join->on('detail_fjs.id_sj', '=', 'surat_jalans.id_sj')->whereColumn('detail_fjs.kode_perusahaan', '=', 'surat_jalans.kode_perusahaan');
                    })->select('detail_fjs.*', 'surat_jalans.tanggal_sj', 'surat_jalans.jatuh_tempo')->get();
                    $paktur = detail_faktur::join('penerimaan_barangs', function ($join) {
                        $join->on('detail_fakturs.id_pb', '=', 'penerimaan_barangs.id_pb')->whereColumn('detail_fakturs.kode_perusahaan', '=', 'penerimaan_barangs.kode_perusahaan');
                    })->select('detail_fakturs.*', 'penerimaan_barangs.tanggal_pb', 'penerimaan_barangs.jatuh_tempo')->get();
                    $Perusahaan = Perusahaan::all();
                    $akun = BukuBesar::with('subBukuBesar')->get();

                    $detail = detail_pembayaran::all();

                    // dd($kurangfaktur);

                    return view('barang.pembayaran.bayar', compact('id_bayar', 'akun', 'Perusahaan', 'faktur', 'paktur', 'tanggalHariIni', 'detail'));
                } else {
                    return [
                        'status' => 'error',
                        'message' => 'User not found',
                    ];
                }
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function bayartahap1(Request $request, $id, $id_bayar)
    {

        try {
            $jumlahnilais = 0;
            // dd($request->input());
            // $cbArray = $request->get('cb');
            // dd($cbArray);
            foreach ($request->get('cb') as $key => $cb) {
                $validator = detail_pembayaran::where('id_faktur', $request->get('cb'))->first();
                $nilaifaktur = $request->get('nilaifaktur')[$key];
                $jumlahnilai = $request->get('jumlahnilai')[$key];
                $sisa = $request->get('sisa')[$key];
                $sisatotal = $sisa - $jumlahnilai;
                if ($validator === null) {
                    detail_pembayaran::create([
                        'user_id' => $id,
                        'id_bayar'  => $id_bayar,
                        'id_faktur' => $cb,
                        'no_bukubesar' => $request->get('no_akun'),
                        'konsumen' => $request->get('no_konsumen'),
                        'nilai_faktur' => $nilaifaktur,
                        'jumlah_pembayaran' => $jumlahnilai,
                        'sisa_pembayaran' => $sisa,
                    ]);
                } else {
                    $sisaTerbaru = $nilaifaktur - ($nilaifaktur - $sisa);
                    detail_pembayaran::where('id_faktur', $cb)->update(['sisa_pembayaran' => $sisaTerbaru]);
                }
                $jumlahnilais += $jumlahnilai;
            }
            $jumlahnilais += $jumlahnilai;
            triad_pembayaran::create([
                'user_id' => $id,
                'id_bayar' => $id_bayar,
                'no_akun' => $request->get('no_akun'),
                'no_konsumen' => $request->get('no_konsumen'),
                'total_pembayaran' => $jumlahnilai, // Use correct syntax for assignment
            ]);
            // return redirect('/autojurnal/' . $id_bayar . '/' . $id)->with('success', 'Silahkan selesaikan pembayaran');
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function bayartahap2($id_bayar, $id)
    {
        try {
            $autojurnal = autojurnal::where('user_id', $id)->first();
            if ($autojurnal === null) {
                return redirect('/setting')->with('warning', 'Silahkan setting akun terlebih dahulu');
            } else {
                $detailbayars = detail_pembayaran::where('id_bayar', $id_bayar)->where('user_id', $id)->get();
                $autoakun = autojurnal::where('user_id', auth()->user()->id)->first();
                $autoakundebit = SubBukuBesar::where('no_subbukubesar', $autoakun->akun_debit)->first();
                $autoakunkredit = SubBukuBesar::where('no_subbukubesar', $autoakun->akun_kredit)->first();

                $fakturs = [];
                if ($detailbayars->isNotEmpty()) {
                    foreach ($detailbayars as $detailbayar) {
                        $perusahaans = Perusahaan::where('kode_perusahaan', $detailbayar->konsumen)->first();
                        $akuns = SubBukuBesar::where('no_subbukubesar', $detailbayar->no_bukubesar)->first();
                        $fb = faktur_beli::where('id_fb', $detailbayar->id_faktur)->first();
                        $fj = faktur_jual::where('id_fj', $detailbayar->id_faktur)->first();

                        if ($fb !== null) {
                            $fakturs[] = $fb;
                        }

                        if ($fj !== null) {
                            $fakturs[] = $fj;
                        }
                    }
                    if (!empty($fakturs)) {
                        return view('barang.pembayaran.autojurnal', compact('id_bayar', 'akuns', 'perusahaans', 'fakturs', 'detailbayars', 'autoakundebit', 'autoakunkredit', 'id'));
                    } else {
                        return redirect()->back()->with('error', 'Data faktur tidak ditemukan');
                    }
                } else {
                    return [
                        'status' => 'error',
                        'message' => 'Hmm.. tidak ada auto jurnal yang bisa diproses. Silahkan lakukan pembayaran',
                    ];
                }
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(), // Return the actual error message for debugging
            ];
        }
    }

    public function bayartahap2create(Request $request, $id, $id_bayar)
    {
        try {
            triad_pembayaran::where('id_bayar', $id_bayar)->where('user_id', $id)->update(['status_autojurnal' => 'no']);
            $jumlah = detail_pembayaran::where('id_bayar', $id_bayar)->where('user_id', $id)->first();
            // Initialize $jumlah outside the loop to accumulate the total amount    
            foreach ($request->get('autoakun') as $key => $autoakun) {
                $nama_akun = SubBukuBesar::where('no_subbukubesar', $autoakun)->first();
                $debet = $request->has('debet') ? $request->get('debet')[$key] : 0;
                $kredit = $request->has('kredit') ? $request->get('kredit')[$key] : 0;
                $akun_pembantu = $request->has('akunpembantu') ? $request->get('akunpembantu')[$key] : '-';
                $jumlah = $request->has('jumlah') ? $request->get('jumlah')[$key] : 0;
                pembayaran::create([
                    'id_bayar' => $id_bayar,
                    'no_akun' => $autoakun,
                    'nama_akun' => $nama_akun->ket,
                    'debit' => $debet,
                    'kredit' => $kredit,
                    'keterangan' => $request->get('ket')[$key],
                    'kurs' => 0,
                    'jumlah' => $jumlah,
                    'akun_pembantu' => $akun_pembantu,
                    'departemen' => '-',
                    'nama_karyawan' => '-'
                ]);
            }
            return [
                'status' => 'success',
                'message' => 'Payment details created successfully',
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
