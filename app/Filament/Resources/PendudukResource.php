<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendudukResource\Pages;
use App\Models\Penduduk;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Resource Filament untuk mengelola data master Kependudukan (Penduduk).
 * Menyediakan form pengisian data identitas dan tabel pelaporan lengkap.
 */
class PendudukResource extends Resource
{
    protected static ?string $model = Penduduk::class;

    protected static ?string $recordTitleAttribute = 'nama_lengkap';

    public static function getGloballySearchableAttributes(): array
    {
        return ['nik', 'nama_lengkap', 'no_kk'];
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Data Kependudukan';

    protected static ?string $navigationLabel = 'Data Penduduk';

    protected static ?int $navigationSort = 3;

    /**
     * Membangun form isian data penduduk.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identitas Pribadi')
                ->description('Data identitas dasar penduduk.')
                ->icon('heroicon-o-identification')
                ->schema([
                    TextInput::make('nik')
                        ->label('NIK')
                        ->required()
                        ->length(16),
                    TextInput::make('no_kk')
                        ->label('No. KK')
                        ->required()
                        ->length(16),
                    TextInput::make('nama_lengkap')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(100),
                    TextInput::make('tempat_lahir')
                        ->label('Tempat Lahir')
                        ->required()
                        ->maxLength(50),
                    DatePicker::make('tanggal_lahir')
                        ->label('Tanggal Lahir')
                        ->required()
                        ->displayFormat('d M Y'),
                    Select::make('jenis_kelamin')
                        ->label('Jenis Kelamin')
                        ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                        ->required(),
                ])->columns(1)->columnSpanFull(),

            /**
             * Bagian kedua form untuk menampilkan data kependudukan.
             */
            Section::make('Data Kependudukan')
                ->description('Informasi status kependudukan.')
                ->icon('heroicon-o-building-library')
                ->schema([
                    TextInput::make('agama')->required(),
                    TextInput::make('pendidikan')->label('Pendidikan Terakhir')->required(),
                    TextInput::make('pekerjaan')->required(),
                    TextInput::make('status_perkawinan')->label('Status Perkawinan')->required(),
                    TextInput::make('status_keluarga')->label('Status dalam Keluarga')->required(),
                    Select::make('status_mutasi')
                        ->label('Status Mutasi')
                        ->options(['Tetap' => 'Tetap', 'Pindah' => 'Pindah', 'Meninggal' => 'Meninggal'])
                        ->required(),
                    TextInput::make('telegram_chat_id')
                        ->label('Telegram Chat ID')
                        ->maxLength(50),
                    TextInput::make('no_hp')
                        ->label('No. HP / WhatsApp')
                        ->helperText('Format: 62812xxxx (tanpa +, gunakan awalan 62). Digunakan untuk notifikasi WhatsApp.')
                        ->tel()
                        ->maxLength(20),
                ])->columns(1)->columnSpanFull(),
        ]);
    }

    /**
     * Membangun tabel data penduduk lengkap.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nik')
                    ->label('NIK')
                    ->sortable()
                    ->copyable()
                    ->copyMessage('NIK disalin!'),
                TextColumn::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('jenis_kelamin')
                    ->label('L/P')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'L' ? 'info' : 'danger')
                    ->formatStateUsing(fn (string $state): string => $state === 'L' ? 'Laki-laki' : 'Perempuan'),
                TextColumn::make('keluarga.dusun')
                    ->label('Dusun'),
                TextColumn::make('status_mutasi')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Tetap' => 'success',
                        'Pindah' => 'warning',
                        'Meninggal' => 'danger',
                        default => 'gray',
                    }),
            ])
            /**
             * Filter untuk menyaring data penduduk berdasarkan status mutasi dan jenis kelamin.
             */
            ->filters([
                SelectFilter::make('status_mutasi')
                    ->label('Status')
                    ->options(['Tetap' => 'Tetap', 'Pindah' => 'Pindah', 'Meninggal' => 'Meninggal']),
                SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options(['L' => 'Laki-laki', 'P' => 'Perempuan']),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->actionsColumnLabel('Aksi')
            ->defaultSort('nama_lengkap')
            ->striped()
            ->emptyStateHeading('Belum Ada Data Penduduk')
            ->emptyStateDescription('Tambahkan data penduduk melalui tombol di atas.')
            ->emptyStateIcon('heroicon-o-user-group');
    }

    /**
     * Mengembalikan query builder yang sudah dimuat relasi keluarga.
     */
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['keluarga']);
    }

    /**
     * Mengembalikan daftar halaman yang tersedia untuk resource ini.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenduduk::route('/'),
            'create' => Pages\CreatePenduduk::route('/create'),
            'edit' => Pages\EditPenduduk::route('/{record}/edit'),
        ];
    }
}
