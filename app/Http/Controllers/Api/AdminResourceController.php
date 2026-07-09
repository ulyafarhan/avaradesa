<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\KategoriSurat;
use App\Models\InventarisFasilitas;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdminResourceController extends Controller
{
    // ── KELUARGA ─────────────────────────────────────────────────────────────
    public function keluargaIndex(Request $request)
    {
        $query = Keluarga::with('kepalaKeluarga');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('no_kk', 'like', "%{$s}%")
                  ->orWhere('alamat', 'like', "%{$s}%")
                  ->orWhere('rt', 'like', "%{$s}%")
                  ->orWhere('rw', 'like', "%{$s}%");
        }
        $data = $query->paginate($request->per_page ?? 15);
        return response()->json([
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page'    => $data->lastPage(),
                'per_page'     => $data->perPage(),
                'total'        => $data->total(),
            ]
        ]);
    }

    public function keluargaStore(Request $request)
    {
        $v = $request->validate([
            'no_kk' => 'required|string|size:16|unique:keluarga,no_kk',
            'alamat' => 'required|string',
            'rt' => 'nullable|string',
            'rw' => 'nullable|string',
            'dusun' => 'nullable|string',
        ]);
        $k = Keluarga::create($v);
        return response()->json(['message' => 'Kartu Keluarga berhasil dibuat', 'data' => $k], 201);
    }

    public function keluargaDestroy($no_kk)
    {
        Keluarga::where('no_kk', $no_kk)->delete();
        return response()->json(['message' => 'Data KK berhasil dihapus']);
    }

    // ── KATEGORI SURAT ───────────────────────────────────────────────────────
    public function kategoriSuratIndex()
    {
        $data = KategoriSurat::orderBy('nama_surat')->get();
        return response()->json(['data' => $data]);
    }

    public function kategoriSuratStore(Request $request)
    {
        $v = $request->validate([
            'kode_surat' => 'required|string|unique:kategori_surat,kode_surat',
            'nama_surat' => 'required|string',
            'syarat_dokumen' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        $k = KategoriSurat::create($v);
        return response()->json(['message' => 'Kategori surat berhasil dibuat', 'data' => $k], 201);
    }

    public function kategoriSuratUpdate(Request $request, $id)
    {
        $k = KategoriSurat::findOrFail($id);
        $v = $request->validate([
            'kode_surat' => 'required|string|unique:kategori_surat,kode_surat,'.$id,
            'nama_surat' => 'required|string',
            'syarat_dokumen' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        $k->update($v);
        return response()->json(['message' => 'Kategori surat berhasil diperbarui', 'data' => $k]);
    }

    public function kategoriSuratDestroy($id)
    {
        KategoriSurat::destroy($id);
        return response()->json(['message' => 'Kategori surat berhasil dihapus']);
    }

    // ── INVENTARIS & FASILITAS ────────────────────────────────────────────────
    public function fasilitasIndex(Request $request)
    {
        $query = InventarisFasilitas::query();
        if ($request->filled('search')) {
            $query->where('nama_fasilitas', 'like', "%{$request->search}%");
        }
        $data = $query->paginate(15);
        return response()->json([
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page'    => $data->lastPage(),
                'per_page'     => $data->perPage(),
                'total'        => $data->total(),
            ]
        ]);
    }

    public function fasilitasStore(Request $request)
    {
        $v = $request->validate([
            'nama_fasilitas' => 'required|string',
            'kategori' => 'required|string',
            'kondisi' => 'required|string',
            'lokasi' => 'nullable|string',
            'jumlah' => 'required|integer',
        ]);
        $f = InventarisFasilitas::create($v);
        return response()->json(['message' => 'Inventaris fasilitas berhasil ditambahkan', 'data' => $f], 201);
    }

    public function fasilitasDestroy($id)
    {
        InventarisFasilitas::destroy($id);
        return response()->json(['message' => 'Fasilitas berhasil dihapus']);
    }

    // ── AUDIT LOGS ───────────────────────────────────────────────────────────
    public function auditLogIndex(Request $request)
    {
        $data = AuditLog::orderByDesc('created_at')->paginate(20);
        return response()->json([
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page'    => $data->lastPage(),
                'per_page'     => $data->perPage(),
                'total'        => $data->total(),
            ]
        ]);
    }
}
