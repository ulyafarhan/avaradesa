<?php

namespace App\Filament\Resources;

use App\Events\MutasiStatusUpdated;
use App\Filament\Resources\MutasiPendudukResource\Pages;
use App\Models\MutasiPenduduk;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Resource Filament untuk mengelola data permohonan mutasi kependudukan warga.
 */
class MutasiPendudukResource extends Resource
{
    protected static ?string $model = MutasiPenduduk::class;

    protected static ?string $recordTitleAttribute = 'nik';

    public static function getGloballySearchableAttributes(): array
    {
        return ['nik', 'penduduk.nama_lengkap', 'jenis_mutasi'];
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static string|\UnitEnum|null $navigationGroup = 'Layanan';

    protected static ?string $navigationLabel = 'Mutasi Penduduk';

    protected static ?int $navigationSort = 2;

    /**
     * Menampilkan jumlah mutasi pending sebagai badge navigasi.
     */
    public static function getNavigationBadge(): ?string
    {
        $count = MutasiPenduduk::query()->pending()->count();
        return $count > 0 ? (string) $count : null;
    }

    /**
     * Warna badge navigasi.
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    /**
     * Membangun form isian data mutasi penduduk.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Data Mutasi')
                ->description('Informasi mutasi penduduk.')
                ->icon('heroicon-o-arrow-path')
                ->schema([
                    Select::make('nik')
                        ->relationship('penduduk', 'nama_lengkap')
                        ->label('Penduduk')
                        ->searchable()
                        ->required(),
                    Select::make('jenis_mutasi')
                        ->label('Jenis Mutasi')
                        ->options([
                            'Kelahiran' => 'Kelahiran',
                            'Kematian' => 'Kematian',
                            'Kedatangan' => 'Kedatangan',
                            'Kepindahan' => 'Kepindahan',
                        ])
                        ->required(),
                    DatePicker::make('tanggal_mutasi')
                        ->label('Tanggal Mutasi')
                        ->required()
                        ->displayFormat('d M Y'),
                    Select::make('status_verifikasi')
                        ->label('Status Verifikasi')
                        ->options([
                            'Pending' => 'Pending',
                            'Disetujui' => 'Disetujui',
                            'Ditolak' => 'Ditolak',
                        ])
                        ->required(),
                ])->columns(1)->columnSpanFull(),

            Section::make('Keterangan')
                ->schema([
                    Textarea::make('keterangan')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),
                    TextInput::make('dokumen_bukti')
                        ->label('Dokumen Bukti')
                        ->required()
                        ->columnSpanFull(),
                ])->columnSpanFull(),
        ]);
    }

    /**
     * Membangun tabel daftar mutasi penduduk dengan aksi setujui/tolak.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->poll('15s')
            ->columns([
                TextColumn::make('penduduk.nama_lengkap')
                    ->label('Penduduk')
                    ->weight('bold'),
                TextColumn::make('jenis_mutasi')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Kelahiran' => 'success',
                        'Kematian' => 'danger',
                        'Kedatangan' => 'info',
                        'Kepindahan' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('tanggal_mutasi')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status_verifikasi')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status_verifikasi')
                    ->label('Status')
                    ->options(['Pending' => 'Pending', 'Disetujui' => 'Disetujui', 'Ditolak' => 'Ditolak']),
                SelectFilter::make('jenis_mutasi')
                    ->label('Jenis')
                    ->options(['Kelahiran' => 'Kelahiran', 'Kematian' => 'Kematian', 'Kedatangan' => 'Kedatangan', 'Kepindahan' => 'Kepindahan']),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (MutasiPenduduk $record): bool => $record->status_verifikasi === 'Pending')
                    ->requiresConfirmation()
                    ->action(function (MutasiPenduduk $record): void {
                        $oldStatus = $record->status_verifikasi;
                        $record->update([
                            'status_verifikasi' => 'Disetujui',
                            'diverifikasi_oleh' => auth('admin')->id(),
                        ]);
                        MutasiStatusUpdated::dispatch($record, $oldStatus, 'Disetujui');
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (MutasiPenduduk $record): bool => $record->status_verifikasi === 'Pending')
                    ->requiresConfirmation()
                    ->action(function (MutasiPenduduk $record): void {
                        $oldStatus = $record->status_verifikasi;
                        $record->update([
                            'status_verifikasi' => 'Ditolak',
                            'diverifikasi_oleh' => auth('admin')->id(),
                        ]);
                        MutasiStatusUpdated::dispatch($record, $oldStatus, 'Ditolak');
                    }),
                \Filament\Actions\EditAction::make(),
                DeleteAction::make(),
            ])
            ->actionsColumnLabel('Aksi')
            ->defaultSort('tanggal_mutasi', 'desc')
            ->striped()
            ->emptyStateHeading('Belum Ada Mutasi')
            ->emptyStateDescription('Data mutasi penduduk akan muncul di sini.')
            ->emptyStateIcon('heroicon-o-arrow-path');
    }

    /**
     * Query dengan eager load relasi penduduk.
     */
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['penduduk']);
    }

    /**
     * Mengembalikan daftar halaman yang tersedia untuk resource ini.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMutasiPenduduk::route('/'),
            'create' => Pages\CreateMutasiPenduduk::route('/create'),
            'edit' => Pages\EditMutasiPenduduk::route('/{record}/edit'),
        ];
    }
}
