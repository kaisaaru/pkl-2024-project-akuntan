<?php

namespace App\Repository\Akun;

use App\Models\User;
use App\Models\TipeAkun;
use App\Models\Perusahaan;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use illuminate\Support\Facades\DB;

class AkunRepository
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function akun()
    {
        $kategori = User::distinct()->pluck('kategori')->filter()->toArray();
        return view('account.akun', ['users' => User::paginate(15)], compact('kategori'));
    }
    public function subakun()
    {
        return view('account.subakun');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        try {
            $validateData = $request->validate([
                'name' => 'required|min:5',
                'username' => 'required|min:5|max:20',
                'password' => 'required|min:5|max:20',
            ]);

            $validateData['password'] = Hash::make($validateData['password']);

            User::create($validateData);
            return redirect('/user')->with('success', 'Berhasil daftar');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(user $user)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $user = User::where('username', $id)->first();
            $a = 'User';
            $b = 'Update User';
            return view('partials.users.userup', compact('user', 'a', 'b'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\UpdateuserRequest  $request
     * @param  \App\Models\user  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $req = $request->validate([
            'name' => 'required|min:5',
            'username' => 'required|min:5|max:20',
            'password' => 'required|min:5|max:20',
        ]);
        $req['password'] = Hash::make($req['password']);
        try {
            User::where('username', $id)->update($req);
            return redirect('/user')->with('success', 'Data berhasil diupdate');
        } catch (QueryException) {
            return redirect('/user')->with('error', 'Terjadi kesalahan');
        }
    }

    public function akunfilter(Request $request)
    {
        try {
            $kategori = User::distinct()->pluck('kategori')->filter()->toArray();
            $kategoriFilter = $request->input('filter', []);

            if (!empty($kategoriFilter)) {
                $filter = User::whereIn('kategori', $kategoriFilter)->paginate(15);
            } else {
                $filter = User::paginate(15);
            }

            return view('account.akun', ['users' => $filter], compact('kategori'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }





    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\user  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $id = User::where('username', $id)->delete();
            return redirect('/user')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateakun($id, Request $request)
    {
        // Validasi request
        $request->validate([
            // 'username' => 'required|string|max:255',
            // 'email' => 'required|email|max:255',
            'name' => 'required|string|max:255',
        ]);

        try {
            // Temukan user berdasarkan ID
            $user = User::findOrFail($id);

            // Update data user
            $user->update([
                // 'username' => $request->input('username'),
                // 'email' => $request->input('email'),
                'name' => $request->input('name'),
            ]);

            return redirect()->back();
            // return redirect()->route('/profile-edit')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            // Handle exception jika terjadi kesalahan
            return redirect()->back()->with('error', 'Failed to update user. ' . $e->getMessage());
        }
    }

    public function updateperusahaan($id, Request $request)
    {
        // Validasi request
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'nama_pimpinan' => 'required|string|max:255',
            'alamat_kantor' => 'required|string|max:255',
            'alamat_gudang' => 'required|string|max:255',
        ]);

        try {
            // Temukan perusahaan berdasarkan kode_perusahaan
            $perusahaan = Perusahaan::findOrFail($id);

            // Update data perusahaan
            $perusahaan->update([
                'nama_perusahaan' => $request->get('nama_perusahaan'),
                'nama_pimpinan' => $request->get('nama_pimpinan'),
                'alamat_kantor' => $request->get('alamat_kantor'),
                'alamat_gudang' => $request->get('alamat_gudang'),
            ]);

            return redirect()->back();

            // return redirect()->route('/profile-edit')->with('success', 'Perusahaan updated successfully.');
        } catch (\Exception $e) {
            // Handle exception jika terjadi kesalahan
            return redirect()->back()->with('error', 'Failed to update perusahaan. ' . $e->getMessage());
        }
    }

    public function tipe()
    {
        return view('bukubesar.tipe.create');
    }

    public function tipecreate(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama_tipe' => 'required|string',
            ]);

            TipeAkun::create([
                'tipe' => $validatedData['nama_tipe'],
            ]);
            return redirect()->back()->with('success', 'Failed to update perusahaan. ');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
