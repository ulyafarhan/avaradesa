<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InformasiPublik;
use App\Models\AuditLog;
use Illuminate\Http\Request;

/**
 * Controller untuk mengelola data artikel berita dan pengumuman desa lewat API.
 *
 * @group Informasi Publik
 */
class InformasiPublikController extends Controller
{
    /**
     * Mengambil daftar seluruh artikel informasi publik yang telah terbit.
     *
     * @unauthenticated
     *
     * @queryParameter kategori string Filter berdasarkan kategori artikel. Contoh: Pengumuman, Berita, Pengumuman.
     *
     * @responseField current_page int Halaman saat ini.
     * @responseField data array Daftar artikel informasi publik.
     * @responseField data[].id int ID artikel.
     * @responseField data[].judul string Judul artikel.
     * @responseField data[].slug string Slug unik artikel.
     * @responseField data[].konten string Isi konten artikel.
     * @responseField data[].kategori string Kategori artikel.
     * @responseField data[].author object Data penulis artikel.
     * @responseField per_page int Jumlah item per halaman (10).
     *
     * @response {
     *   "current_page": 1,
     *   "data": [
     *     {
     *       "id": 1,
     *       "judul": "Pengumuman Pemilihan Kepala Desa 2026",
     *       "slug": "pengumuman-pemilihan-kepala-desa-2026",
     *       "konten": "Diumumkan kepada seluruh warga Desa Udeung...",
     *       "kategori": "Pengumuman",
     *       "cover_image": "images/pemilihan.jpg",
     *       "author": {
     *         "id": 1,
     *         "nama_lengkap": "Administrator Desa"
     *       }
     *     }
     *   ],
     *   "per_page": 10
     * }
     */
    public function index(Request $request)
    {
        $query = InformasiPublik::published()->with('author');

        if ($request->has('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $informasi = $query->orderByDesc('created_at')->paginate(10);

        return response()->json($informasi);
    }

    /**
     * Menampilkan detail informasi publik tertentu berdasarkan slug.
     *
     * @unauthenticated
     *
     * @urlParameter slug string Slug unik artikel informasi publik. Contoh: pengumuman-pemilihan-kepala desa-2026.
     *
     * @responseField data object Detail artikel informasi publik.
     * @responseField data.id int ID artikel.
     * @responseField data.judul string Judul artikel.
     * @responseField data.slug string Slug unik artikel.
     * @responseField data.konten string Isi konten artikel.
     * @responseField data.kategori string Kategori artikel.
     * @responseField data.author object Data penulis artikel.
     *
     * @response {
     *   "data": {
     *     "id": 1,
     *     "judul": "Pengumuman Pemilihan Kepala Desa 2026",
     *     "slug": "pengumuman-pemilihan-kepala-desa-2026",
     *     "konten": "Diumumkan kepada seluruh warga Desa Udeung...",
     *     "kategori": "Pengumuman",
     *     "cover_image": "images/pemilihan.jpg",
     *     "is_published": true,
     *     "author": {
     *       "id": 1,
     *       "nama_lengkap": "Administrator Desa"
     *     }
     *   }
     * }
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika artikel dengan slug tidak ditemukan.
     */
    public function show($slug)
    {
        $informasi = InformasiPublik::where('slug', $slug)
            ->published()
            ->with('author')
            ->firstOrFail();

        return response()->json([
            'data' => $informasi,
        ]);
    }

    /**
     * Mengambil daftar informasi publik untuk keperluan panel admin (termasuk draf).
     *
     * @group Administrasi
     * @subgroup Informasi
     * @authenticated
     *
     * @queryParameter is_published boolean Filter berdasarkan status publikasi. Nilai: true/false.
     *
     * @responseField current_page int Halaman saat ini.
     * @responseField data array Daftar artikel informasi publik untuk admin.
     * @responseField data[].id int ID artikel.
     * @responseField data[].judul string Judul artikel.
     * @responseField data[].is_published boolean Status publikasi artikel.
     * @responseField data[].author object Data penulis artikel.
     * @responseField per_page int Jumlah item per halaman (20).
     *
     * @response {
     *   "current_page": 1,
     *   "data": [
     *     {
     *       "id": 1,
     *       "judul": "Pengumuman Pemilihan Kepala Desa 2026",
     *       "slug": "pengumuman-pemilihan-kepala desa-2026",
     *       "is_published": true,
     *       "kategori": "Pengumuman",
     *       "author": {
     *         "id": 1,
     *         "nama_lengkap": "Administrator Desa"
     *       }
     *     }
     *   ],
     *   "per_page": 20
     * }
     */
    public function adminIndex(Request $request)
    {
        $query = InformasiPublik::with('author');

        if ($request->has('is_published')) {
            $query->where('is_published', $request->boolean('is_published'));
        }

        $informasi = $query->orderByDesc('created_at')->paginate(20);

        return response()->json($informasi);
    }

    /**
     * Menampilkan detail informasi publik tertentu untuk admin berdasarkan ID.
     *
     * @group Administrasi
     * @subgroup Informasi
     * @authenticated
     */
    public function adminShow($id)
    {
        $informasi = InformasiPublik::findOrFail($id);

        return response()->json([
            'data' => $informasi,
        ]);
    }

    /**
     * Menyimpan artikel informasi publik baru ke database.
     *
     * @group Administrasi
     * @subgroup Informasi
     * @authenticated
     *
     * @bodyParameter judul string required Judul artikel informasi publik. Contoh: Pengumuman Pemilihan Kepala Desa 2026.
     * @bodyParameter konten string required Isi konten artikel.
     * @bodyParameter kategori string required Kategori artikel. Contoh: Pengumuman, Berita, Kegiatan.
     * @bodyParameter cover_image string Path/URL gambar sampul artikel (opsional). Contoh: images/cover.jpg.
     * @bodyParameter is_published boolean Status publikasi artikel (default: false). Contoh: true.
     *
     * @responseField message string Pesan hasil operasi.
     * @responseField data object Data artikel yang berhasil dibuat.
     * @responseField data.id int ID artikel.
     * @responseField data.judul string Judul artikel.
     * @responseField data.slug string Slug unik artikel (otomatis dibuat dari judul).
     * @responseField data.is_published boolean Status publikasi artikel.
     *
     * @response 201 {
     *   "message": "Informasi berhasil dibuat",
     *   "data": {
     *     "id": 1,
     *     "judul": "Pengumuman Pemilihan Kepala Desa 2026",
     *     "slug": "pengumuman-pemilihan-kepala-desa-2026",
     *     "konten": "Diumumkan kepada seluruh warga Desa Udeung...",
     *     "kategori": "Pengumuman",
     *     "cover_image": "images/pemilihan.jpg",
     *     "is_published": true,
     *     "author_id": 1
     *   }
     * }
     *
     * @throws \Illuminate\Validation\ValidationException Jika validasi gagal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'kategori' => 'required|string|max:50',
            'cover_image' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $admin = $request->user();

        $informasi = InformasiPublik::create([
            'judul' => $request->judul,
            'konten' => $request->konten,
            'kategori' => $request->kategori,
            'cover_image' => $request->cover_image,
            'is_published' => $request->boolean('is_published', false),
            'author_id' => $admin->id,
        ]);

        AuditLog::log('admin', $admin->id, 'create', 'informasi_publik', $informasi->id, null, $informasi->toArray());

        return response()->json([
            'message' => 'Informasi berhasil dibuat',
            'data' => $informasi,
        ], 201);
    }

    /**
     * Memperbarui detail artikel informasi publik tertentu berdasarkan ID.
     *
     * @group Administrasi
     * @subgroup Informasi
     * @authenticated
     *
     * @urlParameter id int ID artikel informasi publik yang akan diperbarui. Contoh: 1.
     * @bodyParameter judul string Judul artikel (opsional). Contoh: Judul Baru.
     * @bodyParameter konten string Isi konten artikel (opsional).
     * @bodyParameter kategori string Kategori artikel (opsional). Contoh: Berita.
     * @bodyParameter cover_image string Path/URL gambar sampul (opsional). Contoh: images/new_cover.jpg.
     * @bodyParameter is_published boolean Status publikasi (opsional). Contoh: true.
     *
     * @responseField message string Pesan hasil operasi.
     * @responseField data object Data artikel yang diperbarui.
     * @responseField data.id int ID artikel.
     * @responseField data.judul string Judul artikel terbaru.
     *
     * @response {
     *   "message": "Informasi berhasil diupdate",
     *   "data": {
     *     "id": 1,
     *     "judul": "Judul Baru",
     *     "slug": "judul-baru",
     *     "konten": "Konten artikel yang diperbarui...",
     *     "kategori": "Berita",
     *     "is_published": true
     *   }
     * }
     *
     * @throws \Illuminate\Validation\ValidationException Jika validasi gagal.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika artikel tidak ditemukan.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'string|max:255',
            'konten' => 'string',
            'kategori' => 'string|max:50',
            'cover_image' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $informasi = InformasiPublik::findOrFail($id);
        $admin = $request->user();

        $oldData = $informasi->toArray();
        
        $informasi->update($request->only([
            'judul',
            'konten',
            'kategori',
            'cover_image',
            'is_published',
        ]));

        AuditLog::log('admin', $admin->id, 'update', 'informasi_publik', $informasi->id, $oldData, $informasi->toArray());

        return response()->json([
            'message' => 'Informasi berhasil diupdate',
            'data' => $informasi,
        ]);
    }

    /**
     * Menghapus artikel informasi publik tertentu dari database.
     *
     * @group Administrasi
     * @subgroup Informasi
     * @authenticated
     *
     * @urlParameter id int ID artikel informasi publik yang akan dihapus. Contoh: 1.
     *
     * @responseField message string Pesan hasil operasi.
     *
     * @response {
     *   "message": "Informasi berhasil dihapus"
     * }
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika artikel tidak ditemukan.
     */
    public function destroy($id)
    {
        $informasi = InformasiPublik::findOrFail($id);
        $admin = request()->user();

        $oldData = $informasi->toArray();
        $informasi->delete();

        AuditLog::log('admin', $admin->id, 'delete', 'informasi_publik', $id, $oldData, null);

        return response()->json([
            'message' => 'Informasi berhasil dihapus',
        ]);
    }
}
