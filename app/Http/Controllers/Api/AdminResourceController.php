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
            $s = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
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
        $this->requireAdminRole();
        $v = $request->validate([
            'no_kk' => 'required|string|size:16|unique:keluarga,no_kk',
            'alamat' => 'required|string',
            'rt' => 'nullable|string',
            'rw' => 'nullable|string',
            'dusun' => 'nullable|string',
        ]);
        $k = Keluarga::create($v);
        AuditLog::log('admin', auth()->id(), 'create', 'keluarga', $k->no_kk);
        return response()->json(['message' => 'Kartu Keluarga berhasil dibuat', 'data' => $k], 201);
    }

    public function keluargaDestroy($no_kk)
    {
        $this->requireAdminRole();
        Keluarga::where('no_kk', $no_kk)->delete();
        AuditLog::log('admin', auth()->id(), 'delete', 'keluarga', $no_kk);
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
        $this->requireAdminRole();
        $v = $request->validate([
            'kode_surat' => 'required|string|unique:kategori_surat,kode_surat',
            'nama_surat' => 'required|string',
            'syarat_dokumen' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        $k = KategoriSurat::create($v);
        AuditLog::log('admin', auth()->id(), 'create', 'kategori_surat', $k->id);
        return response()->json(['message' => 'Kategori surat berhasil dibuat', 'data' => $k], 201);
    }

    public function kategoriSuratUpdate(Request $request, $id)
    {
        $this->requireAdminRole();
        $k = KategoriSurat::findOrFail($id);
        $v = $request->validate([
            'kode_surat' => 'required|string|unique:kategori_surat,kode_surat,'.$id,
            'nama_surat' => 'required|string',
            'syarat_dokumen' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        $k->update($v);
        AuditLog::log('admin', auth()->id(), 'update', 'kategori_surat', $k->id);
        return response()->json(['message' => 'Kategori surat berhasil diperbarui', 'data' => $k]);
    }

    public function kategoriSuratDestroy($id)
    {
        $this->requireAdminRole();
        KategoriSurat::destroy($id);
        AuditLog::log('admin', auth()->id(), 'delete', 'kategori_surat', $id);
        return response()->json(['message' => 'Kategori surat berhasil dihapus']);
    }

    // ── INVENTARIS & FASILITAS ────────────────────────────────────────────────
    public function fasilitasIndex(Request $request)
    {
        $query = InventarisFasilitas::query();
        if ($request->filled('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('nama_fasilitas', 'like', "%{$search}%");
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
        $this->requireAdminRole();
        $v = $request->validate([
            'nama_fasilitas' => 'required|string',
            'kategori' => 'required|string',
            'kondisi' => 'required|string',
            'lokasi' => 'nullable|string',
            'jumlah' => 'required|integer',
        ]);
        $f = InventarisFasilitas::create($v);
        AuditLog::log('admin', auth()->id(), 'create', 'inventaris_fasilitas', $f->id);
        return response()->json(['message' => 'Inventaris fasilitas berhasil ditambahkan', 'data' => $f], 201);
    }

    public function fasilitasDestroy($id)
    {
        $this->requireAdminRole();
        InventarisFasilitas::destroy($id);
        AuditLog::log('admin', auth()->id(), 'delete', 'inventaris_fasilitas', $id);
        return response()->json(['message' => 'Fasilitas berhasil dihapus']);
    }

    // ── AUDIT LOGS ───────────────────────────────────────────────────────────
    public function auditLogIndex(Request $request)
    {
        $data = AuditLog::orderByDesc('created_at')->paginate(20);
        $data->getCollection()->transform(fn ($log) => $log->makeHidden(['data_lama', 'data_baru']));
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
