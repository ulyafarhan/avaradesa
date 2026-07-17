<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Kelahiran</title>
    <style>
        @if(isset($is_client) && $is_client)
            @page {
                size: A4;
                margin: 20mm 15mm;
            }
            body {
                padding-top: 0;
            }
            header {
                position: relative;
                top: 0;
                height: auto;
                border-bottom: 3px double #000;
                margin-bottom: 20px;
                padding-bottom: 10px;
            }
        @else
            @page {
                margin: 3.2cm 3cm 3cm 3cm;
            }
            header {
                position: fixed;
                top: -2.4cm;
                left: 0px;
                right: 0px;
                height: 2.2cm;
                border-bottom: 3px double #000;
            }
        @endif
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.35;
            margin: 0;
        }
        .title {
            text-align: center;
            margin: 12px 0;
            text-decoration: underline;
            font-weight: bold;
            font-size: 14pt;
        }
        .nomor {
            text-align: center;
            margin-bottom: 15px;
            font-size: 12pt;
        }
        .content {
            text-align: justify;
            margin: 8px 0;
        }
        .content p {
            margin: 6px 0;
        }
        .biodata {
            margin: 8px 0 8px 30px;
        }
        .biodata table {
            width: 100%;
            border-collapse: collapse;
        }
        .biodata td {
            padding: 2px 0;
            vertical-align: top;
        }
        .biodata td:first-child {
            width: 180px;
        }
        .biodata td:nth-child(2) {
            width: 15px;
        }
        .signature {
            margin-top: 15px;
            float: right;
            text-align: center;
            width: 230px;
            position: relative;
        }
        .signature p {
            margin: 2px 0;
        }
        .qr-code {
            position: absolute;
            bottom: 0px;
            left: 0px;
        }
        .qr-code img {
            width: 75px;
            height: 75px;
        }
        .footer {
            position: absolute;
            bottom: -30px;
            left: 0px;
            right: 0px;
            text-align: center;
            font-size: 7.5pt;
            color: #666;
        }
    </style>
</head>
<body>
    <header>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
            <tr>
                <td style="width: 80px; text-align: left; vertical-align: middle; padding-bottom: 5px;">
                    @php
                        $logoPath = public_path('images/logo-desa.png');
                    @endphp
                    @if(file_exists($logoPath))
                        <img src="{{ $logoPath }}" style="width: 70px; height: auto;" alt="Logo">
                    @endif
                </td>
                <td style="text-align: center; vertical-align: middle; padding-bottom: 5px;">
                    <h4 style="margin: 0; font-size: 11pt; font-weight: bold; text-transform: uppercase; line-height: 1.25;">
                        PEMERINTAH KABUPATEN {{ strtoupper(\App\Models\PengaturanDesa::get('kabupaten', 'CONTOH')) }}
                    </h4>
                    <h4 style="margin: 2px 0 0 0; font-size: 11pt; font-weight: bold; text-transform: uppercase; line-height: 1.25;">
                        KECAMATAN {{ strtoupper(\App\Models\PengaturanDesa::get('kecamatan', 'CONTOH')) }}
                    </h4>
                    <h3 style="margin: 2px 0 0 0; font-size: 13pt; font-weight: bold; text-transform: uppercase; line-height: 1.25;">
                        KANTOR DESA {{ strtoupper(\App\Models\PengaturanDesa::get('nama_desa', 'AVARA')) }}
                    </h3>
                    <p style="margin: 4px 0 0 0; font-size: 7.5pt; font-style: italic; line-height: 1.2;">
                        Alamat: {{ \App\Models\PengaturanDesa::get('alamat', 'Jalan Desa Avara') }}, Kec. {{ \App\Models\PengaturanDesa::get('kecamatan', '-') }}, Kab. {{ \App\Models\PengaturanDesa::get('kabupaten', '-') }}, Kode Pos {{ \App\Models\PengaturanDesa::get('kode_pos', '-') }}
                    </p>
                </td>
                <td style="width: 80px;"></td>
            </tr>
        </table>
    </header>

    <div class="title">
        SURAT KETERANGAN KELAHIRAN
    </div>

    <div class="nomor">
        Nomor: {{ $nomor_surat }}
    </div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini,</p>
        <p style="text-indent: 30px; margin-top: 0;">Kepala Desa {{ \App\Models\PengaturanDesa::get('nama_desa', 'Avara') }} Kecamatan {{ \App\Models\PengaturanDesa::get('kecamatan', '-') }} Kabupaten {{ \App\Models\PengaturanDesa::get('kabupaten', '-') }}, menerangkan bahwa:</p>
    </div>

    <div class="content">
        <p>Telah lahir seorang bayi pada:</p>
    </div>

    <div class="biodata">
        <table>
            <tr>
                <td>Nama Bayi</td>
                <td>:</td>
                <td><strong>{{ $data_isian['nama_bayi'] ?? ($data_isian['Nama Bayi'] ?? '-') }}</strong></td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>{{ $data_isian['jenis_kelamin_bayi'] ?? ($data_isian['Jenis Kelamin'] ?? '-') }}</td>
            </tr>
            <tr>
                <td>Tempat Lahir</td>
                <td>:</td>
                <td>{{ $data_isian['tempat_lahir'] ?? ($data_isian['Tempat Lahir'] ?? '-') }}</td>
            </tr>
            <tr>
                <td>Tanggal Lahir</td>
                <td>:</td>
                <td>{{ isset($data_isian['tanggal_lahir']) ? \Carbon\Carbon::parse($data_isian['tanggal_lahir'])->locale('id')->translatedFormat('d F Y') : ($data_isian['Tanggal Lahir'] ?? '-') }}</td>
            </tr>
        </table>
    </div>

    <div class="content">
        <p>Bayi tersebut adalah anak dari:</p>
    </div>

    <div class="biodata">
        <table>
            <tr>
                <td>Nama Ayah</td>
                <td>:</td>
                <td>{{ $data_isian['nama_ayah'] ?? ($data_isian['Nama Ayah'] ?? '-') }}</td>
            </tr>
            <tr>
                <td>Nama Ibu</td>
                <td>:</td>
                <td>{{ $data_isian['nama_ibu'] ?? ($data_isian['Nama Ibu'] ?? '-') }}</td>
            </tr>
            <tr>
                <td>Alamat Orang Tua</td>
                <td>:</td>
                <td>{{ $pemohon->keluarga->alamat }}, Dusun {{ $pemohon->keluarga->dusun }}, RT/RW {{ $pemohon->keluarga->rt_rw }}</td>
            </tr>
        </table>
    </div>

    <div class="content">
        <p>Surat keterangan ini dibuat untuk keperluan pengurusan Akta Kelahiran.</p>
        
        <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="signature">
        <p>Desa {{ \App\Models\PengaturanDesa::get('nama_desa', 'Avara') }}, {{ $tanggal_surat }}</p>
        <p><strong>Kepala Desa {{ \App\Models\PengaturanDesa::get('nama_desa', 'Avara') }}</strong></p>
        
        @php
            $stempelPath = public_path('images/stempel.png');
        @endphp
        @if(file_exists($stempelPath))
            <img src="{{ $stempelPath }}" style="position: absolute; left: -45px; top: 15px; width: 130px; height: auto; opacity: 0.85;" alt="Stempel">
        @endif

        <br><br><br>
        <p><strong><u>{{ \App\Models\PengaturanDesa::get('nama_kepala_desa', 'Nama Kepala Desa') }}</u></strong></p>
        @if(\App\Models\PengaturanDesa::get('nip_kepala_desa'))
            <p style="font-size: 10pt; margin-top: 2px;">NIP. {{ \App\Models\PengaturanDesa::get('nip_kepala_desa') }}</p>
        @endif
    </div>

    <div class="qr-code">
        <img src="{{ $qr_code_path }}" alt="QR Code">
        <p style="font-size: 7.5pt; margin: 4px 0 0 0; text-align: center; color: #666;">Scan Verifikasi</p>
    </div>

    <div class="footer">
        Dokumen ini ditandatangani secara elektronik dan sah menurut Undang-Undang ITE
    </div>
</body>
</html>
