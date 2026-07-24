<?php

namespace App\Services;

use App\Models\PengajuanSurat;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Service untuk menghasilkan dokumen PDF surat resmi desa.
 *
 * Membuat PDF dari template Blade, menambahkan QR Code, nomor surat,
 * dan menyimpannya ke storage publik.
 */
class PdfGeneratorService
{
    /**
     * Menghasilkan PDF surat untuk pengajuan yang telah disetujui.
     *
     * Proses yang dilakukan:
     * 1. Membuat QR hash dari data pengajuan
     * 2. Menghasilkan QR code dalam format SVG
     * 3. Memastikan logo desa tersedia
     * 4. Menghasilkan nomor surat resmi
     * 5. Merender template Blade ke HTML
     * 6. Mengkonversi HTML ke PDF dengan DomPDF
     * 7. Menyimpan PDF ke storage publik
     *
     * @param  \App\Models\PengajuanSurat  $pengajuan  Model pengajuan surat yang akan diproses
     * @return string  URL publik file PDF yang berhasil dibuat
     * @throws \Exception  Jika terjadi kesalahan saat pembuatan PDF
     */
    public function generateSuratPdf(PengajuanSurat $pengajuan): string
    {
        $qrHash = $this->generateQrHash($pengajuan);
        $pengajuan->update(['qr_hash' => $qrHash]);

        $qrCodePath = $this->generateQrCode($qrHash);

        $this->ensureLogoDownloaded();

        $nomorSurat = $this->generateNomorSurat($pengajuan);

        $ttdPath = public_path('images/ttd-kepala-desa.png');
        $stempelPath = public_path('images/stempel.png');

        $desaData = [
            'nama_provinsi' => strtoupper(\App\Models\PengaturanDesa::get('nama_provinsi', 'JAWA BARAT')),
            'nama_kabupaten' => strtoupper(\App\Models\PengaturanDesa::get('nama_kabupaten', 'KABUPATEN BANDUNG')),
            'nama_kecamatan' => strtoupper(\App\Models\PengaturanDesa::get('nama_kecamatan', 'KECAMATAN SAKETI')),
            'nama_desa' => strtoupper(\App\Models\PengaturanDesa::get('nama_desa', 'DESA SUKAMAKMUR')),
            'alamat_kantor' => \App\Models\PengaturanDesa::get('alamat_kantor', 'Jl. Raya Desa No. 01'),
            'kode_pos' => \App\Models\PengaturanDesa::get('kode_pos', '40231'),
            'nama_kepala_desa' => \App\Models\PengaturanDesa::get('nama_kepala_desa', 'H. Budi Santoso, S.Sos'),
            'nip_kepala_desa' => \App\Models\PengaturanDesa::get('nip_kepala_desa', '19780512 200501 1 003'),
            'logo_desa' => public_path('images/logo-desa.png'),
            'ttd_kepala_desa' => file_exists($ttdPath) ? $ttdPath : null,
            'stempel_desa' => file_exists($stempelPath) ? $stempelPath : null,
        ];

        $data = [
            'pengajuan' => $pengajuan,
            'pemohon' => $pengajuan->pemohon,
            'kategori' => $pengajuan->kategori,
            'data_isian' => $pengajuan->data_isian,
            'qr_code_path' => $qrCodePath,
            'nomor_surat' => $nomorSurat,
            'tanggal_surat' => now()->locale('id')->isoFormat('D MMMM YYYY'),
            'desa' => $desaData,
        ];

        $templateView = 'pdf.surat.' . ($pengajuan->kategori->template_view ?? 'generic');
        if (!view()->exists($templateView)) {
            $templateView = 'pdf.surat.generic';
        }

        $html = view($templateView, $data)->render();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        
        $filename = 'surat_' . $pengajuan->nomor_registrasi . '_' . time() . '.pdf';
        $pdfPath = 'surat/' . $filename;
        
        Storage::disk('public')->put($pdfPath, $pdf->output());

        $pdfUrl = Storage::disk('public')->url($pdfPath);
        $pengajuan->update([
            'file_pdf_url' => $pdfUrl,
            'nomor_surat' => $nomorSurat,
        ]);

        return $pdfUrl;
    }

    /**
     * Mengambil data konteks mentah untuk template surat, berguna untuk native frontend rendering.
     *
     * @param \App\Models\PengajuanSurat $pengajuan
     * @return array
     */
    public function getTemplateData(PengajuanSurat $pengajuan): array
    {
        $qrHash = $pengajuan->qr_hash ?? $this->generateQrHash($pengajuan);
        $qrCodePath = public_path('storage/qrcodes/qr_' . $qrHash . '.svg');

        $ttdPath = public_path('images/ttd-kepala-desa.png');
        $stempelPath = public_path('images/stempel.png');

        $desaData = [
            'nama_provinsi' => strtoupper(\App\Models\PengaturanDesa::get('nama_provinsi', 'JAWA BARAT')),
            'nama_kabupaten' => strtoupper(\App\Models\PengaturanDesa::get('nama_kabupaten', 'KABUPATEN BANDUNG')),
            'nama_kecamatan' => strtoupper(\App\Models\PengaturanDesa::get('nama_kecamatan', 'KECAMATAN SAKETI')),
            'nama_desa' => strtoupper(\App\Models\PengaturanDesa::get('nama_desa', 'DESA SUKAMAKMUR')),
            'alamat_kantor' => \App\Models\PengaturanDesa::get('alamat_kantor', 'Jl. Raya Desa No. 01'),
            'kode_pos' => \App\Models\PengaturanDesa::get('kode_pos', '40231'),
            'nama_kepala_desa' => \App\Models\PengaturanDesa::get('nama_kepala_desa', 'H. Budi Santoso, S.Sos'),
            'nip_kepala_desa' => \App\Models\PengaturanDesa::get('nip_kepala_desa', '19780512 200501 1 003'),
            // Local path for file_exists check in blade
            'logo_desa_path' => public_path('images/logo-desa.png'),
            'ttd_kepala_desa_path' => $ttdPath,
            'stempel_desa_path' => $stempelPath,
            // URL for rendering in client iframe
            'logo_desa' => request()->getSchemeAndHttpHost() . '/images/logo-desa.png',
            'ttd_kepala_desa' => file_exists($ttdPath) ? request()->getSchemeAndHttpHost() . '/images/ttd-kepala-desa.png' : null,
            'stempel_desa' => file_exists($stempelPath) ? request()->getSchemeAndHttpHost() . '/images/stempel.png' : null,
        ];

        return [
            'is_client' => true,
            'pengajuan' => $pengajuan,
            'pemohon' => $pengajuan->pemohon,
            'kategori' => $pengajuan->kategori,
            'data_isian' => $pengajuan->data_isian,
            'qr_code_path' => request()->getSchemeAndHttpHost() . '/storage/qrcodes/qr_' . $qrHash . '.svg',
            'nomor_surat' => $pengajuan->nomor_surat ?? $this->generateNomorSurat($pengajuan),
            'tanggal_surat' => $pengajuan->created_at->locale('id')->isoFormat('D MMMM YYYY'),
            'desa' => $desaData,
        ];
    }

    /**
     * Merender template surat ke format HTML murni untuk keperluan Client-Side Preview.
     *
     * @param \App\Models\PengajuanSurat $pengajuan
     * @return string
     */
    public function renderHtml(PengajuanSurat $pengajuan): string
    {
        $data = $this->getTemplateData($pengajuan);

        $templateView = 'pdf.surat.' . ($pengajuan->kategori->template_view ?? 'generic');
        if (!view()->exists($templateView)) {
            $templateView = 'pdf.surat.generic';
        }

        return view($templateView, $data)->render();
    }

    /**
     * Menghasilkan hash SHA-256 dari data pengajuan untuk QR code.
     *
     * Format hash: nomor_registrasi|nik_pemohon|kode_surat|timestamp
     *
     * @param  \App\Models\PengajuanSurat  $pengajuan  Model pengajuan surat
     * @return string  Hash SHA-256 untuk digunakan sebagai identifikasi unik surat
     */
    protected function generateQrHash(PengajuanSurat $pengajuan): string
    {
        $data = implode('|', [
            $pengajuan->nomor_registrasi,
            $pengajuan->nik_pemohon,
            $pengajuan->kategori->kode_surat,
            $pengajuan->created_at->timestamp,
        ]);

        return hash('sha256', $data);
    }

    /**
     * Membuat gambar QR code dalam format SVG berdasarkan hash.
     *
     * QR code mengarah ke URL verifikasi: {app.url}/verifikasi/{hash}
     *
     * @param  string  $hash  Hash SHA-256 dari data pengajuan
     * @return string  Path lokal file QR code SVG yang baru dibuat
     */
    protected function generateQrCode(string $hash): string
    {
        $verificationUrl = config('app.url') . '/verifikasi/' . $hash;
        
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(200)
            ->generate($verificationUrl);

        $filename = 'qr_' . $hash . '.svg';
        $path = 'qrcodes/' . $filename;
        
        Storage::disk('public')->put($path, $qrCode);

        return Storage::disk('public')->path($path);
    }

    /**
     * Memastikan logo desa tersedia di direktori publik.
     *
     * Mengunduh logo dari Wikimedia Commons jika belum ada di storage lokal.
     * Logo disimpan di public/images/logo-desa.png.
     *
     * @return void
     */
    protected function ensureLogoDownloaded(): void
    {
        $logoDir = public_path('images');
        $logoPath = $logoDir . '/logo-desa.png';
        if (!file_exists($logoPath)) {
            if (!is_dir($logoDir)) {
                @mkdir($logoDir, 0777, true);
            }
            $opts = [
                'http' => [
                    'method' => 'GET',
                    'header' => "User-Agent: AvaraDesaApp/1.0 (http://avaradesa.test; admin@avaradesa.test) PHP/8.3\r\n"
                ],
                'ssl' => [
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                ]
            ];
            $context = stream_context_create($opts);
            $logoContent = @file_get_contents('https://upload.wikimedia.org/wikipedia/commons/4/42/Lambang_Kabupaten_Pidie_Jaya.png', false, $context);
            if ($logoContent !== false) {
                @file_put_contents($logoPath, $logoContent);
            }
        }
    }

    /**
     * Menghasilkan nomor surat resmi berdasarkan format desa.
     *
     * Format nomor surat: {kode_surat}/{counter}/DESA-UDEUNG/{bulan}/{tahun}
     * Counter dihitung dari jumlah pengajuan tahun berjalan per kategori surat.
     *
     * @param  \App\Models\PengajuanSurat  $pengajuan  Model pengajuan surat
     * @return string  Nomor surat resmi dalam format yang ditentukan
     */
    protected function generateNomorSurat(PengajuanSurat $pengajuan): string
    {
        $counter = PengajuanSurat::where('kategori_surat_id', $pengajuan->kategori_surat_id)
            ->whereYear('created_at', now()->year)
            ->count();

        // Get dynamic village name and format it (e.g., "DESA SUKAMAKMUR" -> "DESA-SUKAMAKMUR")
        $namaDesa = strtoupper(\App\Models\PengaturanDesa::get('nama_desa', 'DESA SUKAMAKMUR'));
        $namaDesaFormatted = str_replace(' ', '-', $namaDesa);

        return sprintf(
            '%s/%03d/%s/%s/%s',
            $pengajuan->kategori->kode_surat,
            $counter,
            $namaDesaFormatted,
            strtoupper(now()->locale('id')->monthName),
            now()->year
        );
    }
}
