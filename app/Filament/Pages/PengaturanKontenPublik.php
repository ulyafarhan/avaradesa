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

/**
 * Halaman kustom Filament untuk mengelola konten dan aparatur halaman publik secara langsung.
 */
class PengaturanKontenPublik extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $title = 'Konten Halaman Publik';

    protected static ?string $navigationLabel = 'Konten Halaman Publik';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 9;

    protected string $view = 'filament.pages.pengaturan-konten-publik';

    public ?array $data = [];

    /**
     * Memuat data awal dari tabel pengaturan_frontend.
     */
    public function mount(): void
    {
        $this->form->fill([
            'nama_sekdes' => PengaturanFrontend::get('nama_sekdes', 'Nama Sekretaris Desa'),
            'foto_sekdes' => PengaturanFrontend::get('foto_sekdes'),
            'nama_operator' => PengaturanFrontend::get('nama_operator', 'Nama Operator'),
            'foto_operator' => PengaturanFrontend::get('foto_operator'),
            'foto_kepala_desa' => PengaturanFrontend::get('foto_kepala_desa'),
            'telepon_operator' => PengaturanFrontend::get('telepon_operator', '0812-xxxx-xxxx'),
            'medsos_facebook' => PengaturanFrontend::get('medsos_facebook', 'https://facebook.com'),
            'medsos_instagram' => PengaturanFrontend::get('medsos_instagram', 'https://instagram.com'),
            'medsos_twitter' => PengaturanFrontend::get('medsos_twitter', 'https://twitter.com'),
            'medsos_youtube' => PengaturanFrontend::get('medsos_youtube', 'https://youtube.com'),
            'tahun_anggaran' => PengaturanFrontend::get('tahun_anggaran', date('Y')),
            'alamat_kantor' => PengaturanFrontend::get('alamat_kantor', 'Jalan Utama Desa Udeung, Kecamatan Bandar Baru, Kabupaten Pidie Jaya, Provinsi Aceh'),
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

        Notification::make()
            ->title('Pengaturan Halaman Publik Disimpan')
            ->success()
            ->send();

        $this->redirect(static::getUrl(), navigate: false);
    }
}
