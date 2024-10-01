<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanBarang;
use App\Models\User;
use App\Models\Perusahaan;
use App\Models\SuratJalan;
use App\Repository\Users\UsersRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    protected $usersRepository;

    public function __construct(UsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    public function login()
    {
        try {
            return view('account.login1');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading login view.');
        }
    }

    public function register()
    {
        try {
            return view('account.register1');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading register view.');
        }
    }

    public function registerUser(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required|max:255',
            'username' => ['required', 'max:255', 'unique:users'],
            'password' => ['required', 'max:255', function ($attribute, $value, $fail) {
                // Validate length
                if (strlen($value) < 8) {
                    $fail('Password harus lebih dari 8 karakter.');
                }
                // Validate presence of a number
                if (!preg_match('/[0-9]/', $value)) {
                    $fail('Password harus minimal ada 1 angka. ');
                }
                // Validate presence of a symbol
                if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value)) {
                    $fail('Password harus minimal ada 1 simbol.');
                }
                // Validate presence of an uppercase letter
                if (!preg_match('/[A-Z]/', $value)) {
                    $fail('Password harus ada minimal 1 huruf kapital.');
                }
                // Validate absence of space
                if (preg_match('/\s/', $value)) {
                    $fail('Password harus tidak mengandung spasi.');
                }
            }],
            'email' => 'required|email|unique:users,email',
        ], [
            'email.unique' => 'Email sudah terdaftar.',
        ]);
        try {
            return $this->usersRepository->registerUser($request, $validateData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function loginUser(Request $request)
    {
        try {
            $validateData = $request->validate([
                'username' => 'required|min:1',
                'password' => 'required|min:5',
            ]);
            return $this->usersRepository->loginUser($request, $validateData);
        } catch (\Exception $e) {
            // return redirect()->back()->with('error', 'Error logging in.');
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function home()
    {
        try {
            return $this->usersRepository->home();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading home view.');
        }
    }

    public function homeuser()
    {
        try {
            return $this->usersRepository->homeuser();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading home view.');
        }
    }

    public function changePassword(Request $request)
    {
        $validateData = $request->validate([
            'oldPassword' => 'required',
            'newPassword' => 'required|min:8|max:255',
            'confirmPassword' => 'required|same:newPassword',
        ]);
        try {
            return $this->usersRepository->changePassword($request, $validateData);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error changing password.');
        }
    }

    public function settingAplikasi()
    {
        try {

            return $this->usersRepository->settingAplikasi();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading settingAplikasi view.');
        }
    }

    public function Aplikasiset(Request $request, $id)
    {
        $request->validate([
            'akun_kredit' => 'required',
            'akun_debit' => 'required',
        ]);
        try {
            return $this->usersRepository->Aplikasiset($request, $id);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading settingAplikasi view.');
        }
    }
    public function logout()
    {
        try {
            return $this->usersRepository->logout();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error logging out.');
        }
    }
}
