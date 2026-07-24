<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $kategori->nama_surat ?? 'Surat Keterangan Desa' }}</title>
    <style>
        @page {
            margin: 3.2cm 2.5cm 2.5cm 2.5cm;
        }
        header {
            @if(isset($is_client))
            position: relative;
            margin-bottom: 20px;
            @else
            position: fixed;
            top: -2.4cm;
            left: 0px;
            right: 0px;
            height: 2.2cm;
            @endif
            border-bottom: 3px double #000;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            @if(isset($is_client))
            margin: 20px;
            background: #fff;
            @else
            margin: 0;
            @endif
            color: #000;
        }
        .title {
            text-align: center;
            margin: 12px 0 2px 0;
            text-decoration: underline;
            font-weight: bold;
            font-size: 13pt;
            text-transform: uppercase;
        }
        .nomor {
            text-align: center;
            margin-bottom: 15px;
            font-size: 11pt;
        }
        .content {
            text-align: justify;
            margin: 10px 0;
        }
        .biodata {
            margin: 10px 0 10px 20px;
        }
        .biodata table {
            width: 100%;
            border-collapse: collapse;
        }
        .biodata td {
            padding: 3px 0;
            vertical-align: top;
        }
        .biodata td:first-child {
            width: 180px;
        }
        .biodata td:nth-child(2) {
            width: 15px;
        }
        .signature {
            margin-top: 20px;
            float: right;
            text-align: center;
            width: 230px;
            position: relative;
        }
        .signature p {
            margin: 2px 0;
        }
        .qr-code {
            @if(isset($is_client))
            margin-top: 20px;
            @else
            position: absolute;
            bottom: -10px;
            left: 0px;
            @endif
        }
        .qr-code img {
            width: 75px;
            height: 75px;
        }
        .footer {
            @if(isset($is_client))
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            @else
            position: absolute;
            bottom: -25px;
            left: 0px;
            right: 0px;
            @endif
            text-align: center;
            font-size: 7.5pt;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- KOP SURAT DINAMIS METODE FILAMENT -->
    <header>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 75px; text-align: left; vertical-align: middle;">
                    @if(!empty($desa['logo_desa']) && file_exists($desa['logo_desa_path'] ?? $desa['logo_desa']))
                        <img src="{{ $desa['logo_desa'] }}" style="width: 65px; height: auto;" alt="Logo">
                    @endif
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    <h4 style="margin: 0; font-size: 11pt; font-weight: bold; text-transform: uppercase; line-height: 1.2;">
                        PEMERINTAH {{ $desa['nama_kabupaten'] }}
                    </h4>
                    <h4 style="margin: 2px 0 0 0; font-size: 11pt; font-weight: bold; text-transform: uppercase; line-height: 1.2;">
                        KECAMATAN {{ $desa['nama_kecamatan'] }}
                    </h4>
                    <h3 style="margin: 2px 0 0 0; font-size: 13pt; font-weight: bold; text-transform: uppercase; line-height: 1.2;">
                        KANTOR KEPALA {{ $desa['nama_desa'] }}
                    </h3>
                    <p style="margin: 3px 0 0 0; font-size: 8pt; font-style: italic; line-height: 1.2;">
                        Alamat: {{ $desa['alamat_kantor'] }} Kode Pos {{ $desa['kode_pos'] }}
                    </p>
                </td>
                <td style="width: 75px;"></td>
            </tr>
        </table>
    </header>

    <!-- JUDUL SURAT & NOMOR SURAT -->
    <div class="title">
        {{ $kategori->nama_surat ?? 'SURAT KETERANGAN DESA' }}
    </div>

    <div class="nomor">
        Nomor: {{ $nomor_surat }}
    </div>

    <!-- PARAGRAF PEMBUKA -->
    <div class="content">
        <p>Yang bertanda tangan di bawah ini Kepala {{ $desa['nama_desa'] }} Kecamatan {{ ucwords(strtolower($desa['nama_kecamatan'])) }} {{ ucwords(strtolower($desa['nama_kabupaten'])) }}, menerangkan dengan sebenarnya bahwa:</p>
    </div>

    <!-- BIODATA PEMOHON & DYNAMIC ISIAN FORMULIR (JSON SCHEMA FILAMENT) -->
    <div class="biodata">
        <table>
            <tr>
                <td>Nama Lengkap</td>
                <td>:</td>
                <td><strong>{{ $pemohon->nama_lengkap }}</strong></td>
            </tr>
            <tr>
                <td>NIK / No. KK</td>
                <td>:</td>
                <td>{{ $pemohon->nik }} / {{ $pemohon->no_kk }}</td>
            </tr>
            <tr>
                <td>Tempat / Tgl Lahir</td>
                <td>:</td>
                <td>{{ $pemohon->tempat_lahir }}, {{ $pemohon->tanggal_lahir ? \Carbon\Carbon::parse($pemohon->tanggal_lahir)->locale('id')->isoFormat('D MMMM YYYY') : '-' }}</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>{{ $pemohon->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
                <td>Pekerjaan / Agama</td>
                <td>:</td>
                <td>{{ $pemohon->pekerjaan ?? '-' }} / {{ $pemohon->agama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Alamat Tempat Tinggal</td>
                <td>:</td>
                <td>{{ $pemohon->alamat ?? '-' }}</td>
            </tr>

            <!-- ── DYNAMIC LOOP FORM ISIAN FILAMENT (FLEKSIBEL 100%) ── -->
            @if(!empty($data_isian) && is_array($data_isian))
                @foreach($data_isian as $key => $val)
                    @if($key !== 'keperluan' && !is_array($val) && !empty($val))
                    <tr>
                        <td>{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                        <td>:</td>
                        <td><strong>{{ $val }}</strong></td>
                    </tr>
                    @endif
                @endforeach
            @endif
        </table>
    </div>

    <!-- PARAGRAF PENUTUP & KEPERLUAN -->
    <div class="content">
        @if(!empty($data_isian['keperluan']))
        <p>Surat keterangan ini diterbitkan khusus untuk keperluan: <strong>{{ $data_isian['keperluan'] }}</strong>.</p>
        @endif
        
        <p>Demikian Surat Keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <!-- AREA TANDA TANGAN KEPALA DESA & TTE QR CODE -->
    <div class="signature">
        <p>{{ ucwords(strtolower($desa['nama_desa'])) }}, {{ $tanggal_surat }}</p>
        <p><strong>Kepala {{ $desa['nama_desa'] }}</strong></p>

        <!-- Gambar Stempel Resmi Desa (Jika Ada) -->
        @if(!empty($desa['stempel_desa']) && file_exists($desa['stempel_desa_path'] ?? $desa['stempel_desa']))
            <img src="{{ $desa['stempel_desa'] }}" style="position: absolute; left: -35px; top: 25px; width: 120px; height: auto; opacity: 0.85; z-index: 1;" alt="Stempel">
        @endif

        <!-- Gambar TTD Asli Kepala Desa (Jika Ada) -->
        @if(!empty($desa['ttd_kepala_desa']) && file_exists($desa['ttd_kepala_desa_path'] ?? $desa['ttd_kepala_desa']))
            <div style="margin: 8px 0; position: relative; z-index: 2;">
                <img src="{{ $desa['ttd_kepala_desa'] }}" style="height: 55px; width: auto;" alt="TTD">
            </div>
        @else
            <br><br><br><br>
        @endif

        <p><strong><u>{{ $desa['nama_kepala_desa'] }}</u></strong></p>
        @if(!empty($desa['nip_kepala_desa']))
            <p style="font-size: 9.5pt; margin-top: 2px;">NIP. {{ $desa['nip_kepala_desa'] }}</p>
        @endif
    </div>

    <div class="qr-code">
        <img src="{{ $qr_code_path }}" alt="QR Code TTE">
        <p style="font-size: 7pt; margin: 2px 0 0 0; text-align: center; color: #555;">Scan Verifikasi TTE</p>
    </div>

    <div class="footer">
        Dokumen Resmi Desa Ini Ditandatangani Secara Elektronik (TTE) & Sah Menurut UU ITE
    </div>
</body>
</html>
