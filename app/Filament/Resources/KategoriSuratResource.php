<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriSuratResource\Pages;
use App\Models\KategoriSurat;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Resource Filament untuk mengelola data Master Kategori/Jenis Surat Layanan.
 */
class KategoriSuratResource extends Resource
{
    protected static ?string $model = KategoriSurat::class;

    protected static ?string $recordTitleAttribute = 'nama_surat';

    public static function getGloballySearchableAttributes(): array
    {
        return ['kode_surat', 'nama_surat'];
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Kategori Surat';

    protected static ?int $navigationSort = 6;

    /**
     * Membangun form isian kategori surat.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Kategori')
                ->description('Data dasar kategori surat yang tersedia untuk warga.')
                ->icon('heroicon-o-document-duplicate')
                ->schema([
                    TextInput::make('kode_surat')
                        ->label('Kode Surat')
                        ->required()
                        ->maxLength(20)
                        ->prefixIcon('heroicon-o-hashtag')
                        ->placeholder('Contoh: SK-001'),
                    TextInput::make('nama_surat')
                        ->label('Nama Surat')
                        ->required()
                        ->maxLength(100)
                        ->prefixIcon('heroicon-o-document-text')
                        ->placeholder('Nama lengkap jenis surat'),
                    TextInput::make('template_view')
                        ->label('Template View')
                        ->required()
                        ->maxLength(100)
                        ->prefixIcon('heroicon-o-code-bracket')
                        ->placeholder('Nama file template Blade'),
                    Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true)
                        ->onIcon('heroicon-m-check')
                        ->offIcon('heroicon-m-x-mark')
                        ->onColor('success'),
                ])->columns(2)->columnSpanFull(),

            Section::make('Schema & Persyaratan')
                ->description('Tentukan kolom form isian tambahan dan dokumen persyaratan untuk kategori surat ini.')
                ->icon('heroicon-o-code-bracket-square')
                ->collapsible()
                ->schema([
                    Repeater::make('schema_isian')
                        ->label('Kolom Form Isian Warga')
                        ->schema([
                            TextInput::make('field')
                                ->label('Kunci Data (Teks Kecil & Tanpa Spasi)')
                                ->required()
                                ->placeholder('Contoh: keperluan, nama_usaha')
                                ->rules(['regex:/^[a-z0-9_]+$/']),
                            TextInput::make('label')
                                ->label('Nama Input (Tampil di Form)')
                                ->required()
                                ->placeholder('Contoh: Keperluan Surat'),
                            Select::make('type')
                                ->label('Tipe Kolom')
                                ->options([
                                    'text' => 'Teks Singkat',
                                    'number' => 'Angka',
                                    'textarea' => 'Teks Panjang',
                                    'date' => 'Tanggal',
                                    'select' => 'Pilihan Dropdown',
                                ])
                                ->required()
                                ->live(),
                            Repeater::make('options')
                                ->label('Opsi Pilihan (Hanya jika tipe input adalah Pilihan Dropdown)')
                                ->simple(
                                    TextInput::make('option_val')
                                        ->placeholder('Contoh: Baru')
                                        ->required()
                                )
                                ->visible(fn ($get) => $get('type') === 'select')
                                ->helperText('Masukkan opsi pilihan untuk dropdown di atas.')
                                ->default([]),
                            Toggle::make('required')
                                ->label('Wajib Diisi')
                                ->default(true),
                        ])
                        ->columns(2)
                        ->columnSpanFull()
                        ->helperText('Konfigurasi isian dinamis yang wajib diisi oleh warga saat membuat pengajuan surat ini.')
                        ->default([]),
                    Repeater::make('syarat_dokumen')
                        ->label('Dokumen Persyaratan')
                        ->simple(
                            TextInput::make('nama_dokumen')
                                ->placeholder('Contoh: KTP, Kartu Keluarga')
                                ->required()
                        )
                        ->columnSpanFull()
                        ->helperText('Daftar berkas dokumen yang wajib diunggah/dilampirkan oleh warga.')
                        ->default([]),
                ])->columnSpanFull(),
        ]);
    }

    /**
     * Membangun tabel daftar kategori surat.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_surat')
                    ->label('Kode')
                    ->sortable()
                    ->weight('bold')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('nama_surat')
                    ->label('Nama Surat')
                    ->sortable()
                    ->weight('medium')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->nama_surat),
                TextColumn::make('template_view')
                    ->label('Template')
                    ->color('gray')
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->actionsColumnLabel('Aksi')
            ->striped()
            ->emptyStateHeading('Belum Ada Kategori Surat')
            ->emptyStateDescription('Tambahkan kategori surat baru untuk layanan warga.')
            ->emptyStateIcon('heroicon-o-document-duplicate');
    }

    /**
     * Mengembalikan daftar halaman yang tersedia untuk resource ini.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKategoriSurat::route('/'),
            'create' => Pages\CreateKategoriSurat::route('/create'),
            'edit' => Pages\EditKategoriSurat::route('/{record}/edit'),
        ];
    }
}

