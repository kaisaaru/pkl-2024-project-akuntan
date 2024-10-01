<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TipeAkun;
use App\Models\Perusahaan;
use App\Repository\Akun\AkunRepository;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use illuminate\Support\Facades\DB;

class AkunController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $akunRepository;
    public function __construct(AkunRepository $akunRepository)
    {
        $this->akunRepository = $akunRepository;
    }
    public function akun()
    {
        // $user = auth()->user()->id;
        try {

            return $this->akunRepository->akun();            
        } catch (Exception $e) {
            return  redirect()->back()->with('error', $e);
        }
    }
    public function subakun()
    {
        try {
            return $this->akunRepository->subakun();           
        } catch (Exception $e) {
            return  redirect()->back()->with('error', $e);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     */

    public function store(Request $request)
    {
        try {

            return $this->akunRepository->store($request);        
        } catch (Exception $e) {
            return  redirect()->back()->with('error', $e);
        }
    }


    /**
     * Display the specified resource.
     *
     * 
     */
    public function show(user $user)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *     
     */
    public function edit($id)
    {
        try {

            $result = $this->akunRepository->edit($id);
            if (is_array($result) && array_key_exists('message', $result)) {
                return  redirect()->back()->with(['error' => $result['message']]);
            };

            return $result;
        } catch (Exception $e) {
            return  redirect()->back()->with('error', $e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * 
     */
    public function update(Request $request, $id)
    {

        try {

            $result = $this->akunRepository->update($request, $id);
            if (is_array($result) && array_key_exists('message', $result)) {
                return  redirect()->back()->with(['error' => $result['message']]);
            };

            return $result;
        } catch (Exception $e) {
            return  redirect()->back()->with('error', $e);
        }
    }

    public function akunfilter(Request $request)
    {
        $result = $this->akunRepository->akunfilter($request);
        if (is_array($result) && array_key_exists('message', $result)) {
            return  redirect()->back()->with(['error' => $result['message']]);
        };

        return $result;
    }





    /**
     * Remove the specified resource from storage.
     *
     * 
     */
    public function destroy($id)
    {
        try {

            $result = $this->akunRepository->destroy($id);
            if (is_array($result) && array_key_exists('message', $result)) {
                return  redirect()->back()->with(['error' => $result['message']]);
            };

            return $result;
        } catch (Exception $e) {
            return  redirect()->back()->with('error', $e);
        }
    }

    public function updateakun($id, Request $request)
    {
        // Validasi request
        $validate = $request->validate([
            // 'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'name' => 'required|string|max:255',
        ]);

        try {
            if ($validate) {
                $result = $this->akunRepository->updateakun($id, $request);
                if (is_array($result) && array_key_exists('message', $result)) {
                    return  redirect()->back()->with(['error' => $result['message']]);
                };
            }
            return $result;
        } catch (Exception $e) {
            return  redirect()->back()->with('error', $e);
        }
    }

    public function updateperusahaan($id, Request $request)
    {
        // Validasi request
        $validate = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'nama_pimpinan' => 'required|string|max:255',
            'alamat_kantor' => 'required|string|max:255',
            'alamat_gudang' => 'required|string|max:255',
        ]);

        try {
            if ($validate) {
                $result = $this->akunRepository->updateperusahaan($id, $request);
                if (is_array($result) && array_key_exists('message', $result)) {
                    return  redirect()->back()->with(['error' => $result['message']]);
                };
            }
            return $result;
        } catch (Exception $e) {
            return  redirect()->back()->with('error', $e);
        }
    }

    public function tipe()
    {
        try {

            $result = $this->akunRepository->tipe();
            if (is_array($result) && array_key_exists('message', $result)) {
                return  redirect()->back()->with(['error' => $result['message']]);
            };

            return $result;
        } catch (Exception $e) {
            return  redirect()->back()->with('error', $e);
        }
    }

    public function tipecreate(Request $request)
    {
        try {

            $result = $this->akunRepository->tipecreate($request);
            if (is_array($result) && array_key_exists('message', $result)) {
                return  redirect()->back()->with(['error' => $result['message']]);
            };

            return $result;
        } catch (Exception $e) {
            return  redirect()->back()->with('error', $e);
        }
    }
}
