<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminPendudukController extends Controller
{
    public function index(Request $request)
    {
        $query = Penduduk::query();

        // Pencarian berdasarkan NIK atau Nama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nik', 'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%");
        }

        // Filter berdasarkan status mutasi
        if ($request->filled('status')) {
            $query->where('status_mutasi', $request->status);
        }

        $penduduk = $query->orderBy('nama_lengkap', 'asc')->paginate($request->per_page ?? 15);

        return response()->json([
            'data' => $penduduk->items(),
            'meta' => [
                'current_page' => $penduduk->currentPage(),
                'last_page' => $penduduk->lastPage(),
                'per_page' => $penduduk->perPage(),
                'total' => $penduduk->total(),
            ]
        ]);
    }

    public function show($id)
    {
        $penduduk = Penduduk::findOrFail($id);
        
        return response()->json([
            'data' => $penduduk
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|size:16|unique:penduduk,nik',
            'no_kk' => 'required|string|size:16',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|string',
            'pendidikan' => 'required|string',
            'pekerjaan' => 'required|string',
            'status_perkawinan' => 'required|string',
            'status_keluarga' => 'required|string',
            'status_mutasi' => 'required|string',
        ]);

        $penduduk = Penduduk::create($validated);

        return response()->json([
            'message' => 'Data penduduk berhasil ditambahkan',
            'data' => $penduduk
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $penduduk = Penduduk::findOrFail($id);

        $validated = $request->validate([
            'nik' => ['required', 'string', 'size:16', Rule::unique('penduduk', 'nik')->ignore($penduduk->id)],
            'no_kk' => 'required|string|size:16',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|string',
            'pendidikan' => 'required|string',
            'pekerjaan' => 'required|string',
            'status_perkawinan' => 'required|string',
            'status_keluarga' => 'required|string',
            'status_mutasi' => 'required|string',
        ]);

        $penduduk->update($validated);

        return response()->json([
            'message' => 'Data penduduk berhasil diperbarui',
            'data' => $penduduk
        ]);
    }

    public function destroy($id)
    {
        $penduduk = Penduduk::findOrFail($id);
        
        // Hapus juga akun user terkait jika ada
        if ($penduduk->user) {
            $penduduk->user->delete();
        }
        
        $penduduk->delete();

        return response()->json([
            'message' => 'Data penduduk berhasil dihapus'
        ]);
    }
}
