<?php

namespace App\Repository\Users;

use App\Models\autojurnal;
use App\Models\BukuBesar;
use App\Models\PenerimaanBarang;
use App\Models\Perusahaan;
use App\Models\SubBukuBesar;
use App\Models\SuratJalan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersRepository
{
    public function login()
    {
        try {
            return view('account.login');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading login view.');
        }
    }

    public function register()
    {
        try {
            return view('account.register');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading register view.');
        }
    }

    public function registerUser(Request $request, $validateData)
    {
        // dd($request);
        try {

            $validateData['password'] = Hash::make($validateData['password']);

            // Generate kode_perusahaan
            $latestPerusahaan = Perusahaan::latest()->first();
            $nextId = $latestPerusahaan ? ($latestPerusahaan->id + 1) : 1;
            $idFormatted = str_pad($nextId, 4, '0', STR_PAD_LEFT);
            $perusahaanID = 'A-' . $idFormatted;

            // Buat user
            $user = User::create([
                'name' => $validateData['name'],
                'username' => $validateData['username'],
                'password' => $validateData['password'],
                'email' => $validateData['email'],
                'kode_perusahaan' => $perusahaanID,
            ]);

            // Buat perusahaan
            $nama_perusahaan = "-";
            $jenis = "Admin";
            $alamat_kantor = "-";
            $alamat_gudang = "-";
            $nama_pimpinan = "-";
            $no_telepon = 0;
            $plafon_debit = 0;
            $plafon_kredit = 0;

            $perusahaan = Perusahaan::create([
                'kode_perusahaan' => $perusahaanID,
                'nama_perusahaan' => $nama_perusahaan,
                'jenis' => $jenis,
                'alamat_kantor' => $alamat_kantor,
                'alamat_gudang' => $alamat_gudang,
                'nama_pimpinan' => $nama_pimpinan,
                'no_telepon' => $no_telepon,
                'plafon_debit' => $plafon_debit,
                'plafon_kredit' => $plafon_kredit,
            ]);

            if ($user && $perusahaan) {
                $username = $request->input('username');
                $password = $request->input('password');

                $credentials = [
                    'username' => $username,
                    'password' => $password,
                ];
                // dd($credentials);
                if (Auth::attempt($credentials)) {
                    $request->session()->regenerate();
                    return redirect()->route('verification.notice')->with('success', 'Registrasi sukses, silahkan verifikasi email');
                } else {
                    return back()->with('error', 'Terjadi Kesalahan');
                }
            }
        } catch (\Exception $e) {
            // return redirect()->back()->with('error', 'Error registering user: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }



    public function loginUser(Request $request, $validateData)
    {
        try {
            $request->validate([
                'username' => 'required|min:1',
                'password' => 'required|min:5',
            ]);

            $username = $request->input('username');
            $password = $request->input('password');

            $credentials = [
                'username' => $username,
                'password' => $password,
            ];

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended('/home')->with('success', 'Login Berhasil!');
            } else {
                return back()->with('error', 'Terjadi Kesalahan');
            }

            if (auth()->user()->email_verified_at === null) {
                return route('verification', ['error' => 'Email belum terverifikasi, silahkan verifikasi email']);
            }
        } catch (\Exception $e) {
            // return redirect()->back()->with('error', $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function home()
    {
        try {
            $pb = PenerimaanBarang::latest()
                ->where('status', 'Permohonan')
                ->first();

            $sj = SuratJalan::latest()
                ->where('status', 'Permohonan')
                ->first();

            if ($pb !== null) {
                $createdTime = Carbon::parse($pb->created_at);
                $currentTime = now();
                $timeDiff1 = $createdTime->diffForHumans($currentTime);
                session(['pb' => $pb, 'timeDiff1' => $timeDiff1]);
            } else {
                session(['pb' => null, 'timeDiff1' => null]);
            }

            if ($sj !== null) {
                $createdTime = Carbon::parse($sj->created_at);
                $currentTime = now();
                $timeDiff2 = $createdTime->diffForHumans($currentTime);
                session(['sj' => $sj, 'timeDiff2' => $timeDiff2]);
            } else {
                session(['sj' => null, 'timeDiff2' => null]);
            }

            $pb = session('pb');
            $timeDiff1 = session('timeDiff1');
            $sj = session('sj');
            $timeDiff2 = session('timeDiff2');

            return view('layout.dashboard', compact('pb', 'sj', 'timeDiff1', 'timeDiff2'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading home view.');
        }
    }
    public function homeuser()
    {
        try {
            $pb = PenerimaanBarang::latest()
                ->where('status', 'Permohonan')
                ->first();

            $sj = SuratJalan::latest()
                ->where('status', 'Permohonan')
                ->first();

            if ($pb !== null) {
                $createdTime = Carbon::parse($pb->created_at);
                $currentTime = now();
                $timeDiff1 = $createdTime->diffForHumans($currentTime);
                session(['pb' => $pb, 'timeDiff1' => $timeDiff1]);
            } else {
                session(['pb' => null, 'timeDiff1' => null]);
            }

            if ($sj !== null) {
                $createdTime = Carbon::parse($sj->created_at);
                $currentTime = now();
                $timeDiff2 = $createdTime->diffForHumans($currentTime);
                session(['sj' => $sj, 'timeDiff2' => $timeDiff2]);
            } else {
                session(['sj' => null, 'timeDiff2' => null]);
            }

            $pb = session('pb');
            $timeDiff1 = session('timeDiff1');
            $sj = session('sj');
            $timeDiff2 = session('timeDiff2');

            return view('layout.user', compact('pb', 'sj', 'timeDiff1', 'timeDiff2'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading home view.');
        }
    }

    public function changePassword(Request $request, $validateData)
    {
        try {
            // Validate the input data            
            $user = auth()->user();

            if (Hash::check($validateData['oldPassword'], $user->password)) {
                if (Hash::check($validateData['newPassword'], $user->password)) {
                    $status = 'New password cannot be the same as old password.';
                } else {
                    $user->update([
                        'password' => Hash::make($validateData['newPassword'])
                    ]);
                    $status = 'Success! Password has been changed.';
                }
            } else {
                $status = 'Old password is incorrect.';
            }

            return redirect()->route('home')->with('status', $status);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error changing password.');
        }
    }


    public function settingAplikasi()
    {
        try {

            $autojurnal = autojurnal::where('user_id', auth()->user()->id)->first();
            // dd($autojurnal);
            if ($autojurnal === null) {
                $akun = BukuBesar::all();
                $akuns = BukuBesar::all();
                $debit = SubBukuBesar::where('no_subbukubesar', null)->first();
                $kredit = SubBukuBesar::where('no_subbukubesar', null)->first();
                return view('layout.settingapp', compact('akun', 'akuns', 'debit', 'kredit'));
            } else {
                $akun = BukuBesar::all();
                $akuns = BukuBesar::all();
                $debit = SubBukuBesar::where('no_subbukubesar', $autojurnal->akun_debit)->first();
                $kredit = SubBukuBesar::where('no_subbukubesar', $autojurnal->akun_kredit)->first();
                return view('layout.settingapp', compact('akun', 'akuns', 'debit', 'kredit'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading settingAplikasi view.');
        }
    }

    public function Aplikasiset(Request $request, $id)
    {
        try {

            $cek = autojurnal::where('user_id', $id)->first();
            if ($cek == null) {
                autojurnal::create([
                    'user_id' => $id,
                    'akun_kredit' => $request->get('akun_kredit'),
                    'akun_debit' => $request->get('akun_debit'),
                ]);
            } else {
                $cek->update([
                    'akun_kredit' => $request->get('akun_kredit'),
                    'akun_debit' => $request->get('akun_debit'),
                ]);
            }

            // Return a success response
            return redirect()->back()->with('status', 'Settings updated successfully.');
        } catch (\Exception $e) {
            // Log the error or provide a more specific error message
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function logout()
    {
        try {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect('/');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error logging out.');
        }
    }
}
