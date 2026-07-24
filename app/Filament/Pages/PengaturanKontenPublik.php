<?php

namespace App\Filament\Pages;

use App\Models\PengaturanFrontend;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use App\Services\SystemLogger;

/**
 * Halaman kustom Filament untuk mengelola konten dan aparatur halaman publik secara langsung.
 */
class PengaturanKontenPublik extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $title = 'Konten Halaman Publik';

    protected static ?string $navigationLabel = 'Konten Halaman Publik';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static string|\UnitEnum|null $navigationGroup = 'Informasi Desa';

    protected static ?int $navigationSort = 7;

    protected string $view = 'filament.pages.pengaturan-konten-publik';

    public ?array $data = [];

    /**
     * Memuat data awal dari tabel pengaturan_frontend.
     */
    public function mount(): void
    {
        $this->form->fill([
            'nama_kepala_desa' => PengaturanFrontend::get('nama_kepala_desa') ?? \App\Models\PengaturanDesa::get('nama_kepala_desa', 'Nama Kepala Desa'),
            'foto_kepala_desa' => PengaturanFrontend::get('foto_kepala_desa'),
            'nama_sekdes' => PengaturanFrontend::get('nama_sekdes', 'Nama Sekretaris Desa'),
            'foto_sekdes' => PengaturanFrontend::get('foto_sekdes'),
            'nama_operator' => PengaturanFrontend::get('nama_operator', 'Nama Operator'),
            'foto_operator' => PengaturanFrontend::get('foto_operator'),
            'telepon_operator' => PengaturanFrontend::get('telepon_operator', '0812-xxxx-xxxx'),
            'medsos_facebook' => PengaturanFrontend::get('medsos_facebook', 'https://facebook.com'),
            'medsos_instagram' => PengaturanFrontend::get('medsos_instagram', 'https://instagram.com'),
            'medsos_twitter' => PengaturanFrontend::get('medsos_twitter', 'https://twitter.com'),
            'medsos_youtube' => PengaturanFrontend::get('medsos_youtube', 'https://youtube.com'),
            'tahun_anggaran' => PengaturanFrontend::get('tahun_anggaran', date('Y')),
            'alamat_kantor' => PengaturanFrontend::get('alamat_kantor', 'Jalan Utama Desa Udeung, Kecamatan Bandar Baru, Kabupaten Pidie Jaya, Provinsi Aceh'),
            'foto_kantor' => PengaturanFrontend::get('foto_kantor'),
            'sejarah_desa' => PengaturanFrontend::get('sejarah_desa', 'Desa didirikan sejak masa lampau di kecamatan ini. Nama wilayah disematkan karena letak historis dan geografisnya yang melimpah akan kekayaan alam lokal.\n\nKini, aksesibilitas warga semakin kuat dengan hadirnya jembatan gantung Garuda yang melintasi Sungai Lueng Putu, menghubungkan langsung Desa dengan Desa Ara. Jembatan gantung ini menjadi urat nadi perekonomian, peribadahan santri menuju Pesantren Raudhatul Ulum, serta mobilitas anak-anak menuju SD Negeri yang telah mendidik anak-anak desa sejak tahun 1977.'),
            'kbn_tanggal_resmi' => PengaturanFrontend::get('kbn_tanggal_resmi', 'Senin, 14 Oktober 2024'),
            'kbn_jumlah_desa' => PengaturanFrontend::get('kbn_jumlah_desa', '221 desa'),
            'geo_koordinat' => PengaturanFrontend::get('geo_koordinat', '5.277732, 96.102347'),
            'geo_komoditas' => PengaturanFrontend::get('geo_komoditas', 'Padi, Jagung, Kakao'),
            'geo_batas_utara' => PengaturanFrontend::get('geo_batas_utara', 'Desa Ara (Jembatan)'),
            'geo_batas_selatan' => PengaturanFrontend::get('geo_batas_selatan', 'Desa Blang Baro'),
            'geo_orbitasi' => PengaturanFrontend::get('geo_orbitasi', '3.5 Km ke Lueng Putu'),
        ]);
    }

    /**
     * Skema form pengaturan halaman publik.
     */
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Tabs::make('Pengaturan Konten')
                    ->tabs([
                        Tab::make('Staf & Aparatur Desa')
                            ->icon('heroicon-o-users')
                            ->schema([
                                TextInput::make('nama_kepala_desa')
                                    ->label('Nama Kepala Desa')
                                    ->required()
                                    ->maxLength(150),
                                FileUpload::make('foto_kepala_desa')
                                    ->label('Foto Resmi Kepala Desa')
                                    ->image()
                                    ->directory('desa/aparatur')
                                    ->helperText('Unggah foto kepala desa resmi untuk halaman profil publik.'),
                                TextInput::make('nama_sekdes')
                                    ->label('Nama Sekretaris Desa')
                                    ->required()
                                    ->maxLength(150),
                                FileUpload::make('foto_sekdes')
                                    ->label('Foto Sekretaris Desa')
                                    ->image()
                                    ->directory('desa/aparatur')
                                    ->helperText('Unggah foto sekretaris desa.'),
                                TextInput::make('nama_operator')
                                    ->label('Nama Operator Layanan')
                                    ->required()
                                    ->maxLength(150),
                                FileUpload::make('foto_operator')
                                    ->label('Foto Operator Layanan')
                                    ->image()
                                    ->directory('desa/aparatur')
                                    ->helperText('Unggah foto operator pelayanan desa.'),
                            ]),
                        Tab::make('Kontak & Media Sosial')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                FileUpload::make('foto_kantor')
                                    ->label('Foto Kantor Desa')
                                    ->image()
                                    ->directory('desa/kantor')
                                    ->helperText('Foto gedung kantor desa untuk section alur pengajuan di beranda.'),
                                TextInput::make('telepon_operator')
                                    ->label('Nomor Telepon/WhatsApp Operator')
                                    ->required()
                                    ->maxLength(50)
                                    ->placeholder('Contoh: 081234567890'),
                                Textarea::make('alamat_kantor')
                                    ->label('Alamat Kantor Desa')
                                    ->required()
                                    ->rows(3),
                                TextInput::make('tahun_anggaran')
                                    ->label('Tahun Anggaran Berjalan')
                                    ->required()
                                    ->maxLength(4)
                                    ->placeholder('Contoh: ' . date('Y')),
                                TextInput::make('medsos_facebook')
                                    ->label('Link Facebook Desa')
                                    ->url()
                                    ->maxLength(255),
                                TextInput::make('medsos_instagram')
                                    ->label('Link Instagram Desa')
                                    ->url()
                                    ->maxLength(255),
                                TextInput::make('medsos_twitter')
                                    ->label('Link Twitter/X Desa')
                                    ->url()
                                    ->maxLength(255),
                                TextInput::make('medsos_youtube')
                                    ->label('Link YouTube Desa')
                                    ->url()
                                    ->maxLength(255),
                            ]),
                        Tab::make('Sejarah & Geografis')
                            ->icon('heroicon-o-map')
                            ->schema([
                                Textarea::make('sejarah_desa')
                                    ->label('Sejarah Singkat Desa')
                                    ->rows(6)
                                    ->required(),
                                TextInput::make('geo_koordinat')
                                    ->label('Koordinat Peta (Latitude, Longitude)')
                                    ->required()
                                    ->placeholder('Contoh: 5.277732, 96.102347'),
                                TextInput::make('geo_komoditas')
                                    ->label('Komoditas Utama Lahan')
                                    ->required()
                                    ->placeholder('Contoh: Padi, Jagung, Kakao'),
                                TextInput::make('geo_batas_utara')
                                    ->label('Batas Utara Desa')
                                    ->required(),
                                TextInput::make('geo_batas_selatan')
                                    ->label('Batas Selatan Desa')
                                    ->required(),
                                TextInput::make('geo_orbitasi')
                                    ->label('Orbitasi Simpang (Jarak ke Pusat)')
                                    ->required(),
                            ]),
                        Tab::make('Program Unggulan')
                            ->icon('heroicon-o-star')
                            ->schema([
                                TextInput::make('kbn_tanggal_resmi')
                                    ->label('Tanggal Peresmian Kampung Bebas Narkoba (KBN)')
                                    ->required()
                                    ->placeholder('Contoh: Senin, 14 Oktober 2024'),
                                TextInput::make('kbn_jumlah_desa')
                                    ->label('Jumlah Desa Rujukan KBN di Kabupaten')
                                    ->required()
                                    ->placeholder('Contoh: 221 desa'),
                            ]),
                    ])
            ])
            ->statePath('data');
    }

    /**
     * Menyimpan data pengaturan ke tabel pengaturan_frontend.
     */
    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            PengaturanFrontend::set($key, $value, 'string');
        }

        SystemLogger::log('content.changed', 'Konten halaman publik diperbarui', null, [
            'changed_keys' => array_keys($data),
        ]);

        Notification::make()
            ->title('Pengaturan Halaman Publik Disimpan')
            ->success()
            ->send();

        $this->redirect(static::getUrl(), navigate: false);
    }
}
