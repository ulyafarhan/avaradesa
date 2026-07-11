<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KeluargaResource\Pages;
use App\Models\Keluarga;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Resource Filament untuk mengelola data Kartu Keluarga (KK).
 */
class KeluargaResource extends Resource
{
    protected static ?string $model = Keluarga::class;

    protected static ?string $recordTitleAttribute = 'no_kk';

    public static function getGloballySearchableAttributes(): array
    {
        return ['no_kk', 'dusun', 'alamat'];
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home-modern';

    protected static string|\UnitEnum|null $navigationGroup = 'Data Kependudukan';

    protected static ?string $navigationLabel = 'Data Keluarga';

    protected static ?int $navigationSort = 4;

    /**
     * Membangun form isian data keluarga.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Kartu Keluarga')
                ->description('Data identitas kartu keluarga.')
                ->icon('heroicon-o-home-modern')
                ->schema([
                    TextInput::make('no_kk')
                        ->label('Nomor KK')
                        ->required()
                        ->length(16),
                    Select::make('kepala_keluarga_nik')
                        ->label('Kepala Keluarga')
                        ->relationship('kepalaKeluarga', 'nama_lengkap')
                        ->searchable()
                        ->preload(),
                ])->columns(1)->columnSpanFull(),

            Section::make('Alamat')
                ->schema([
                    Textarea::make('alamat')
                        ->label('Alamat Lengkap')
                        ->required()
                        ->columnSpanFull()
                        ->rows(3),
                    TextInput::make('dusun')->required(),
                    TextInput::make('rt_rw')->label('RT/RW')->required(),
                ])->columns(1)->columnSpanFull(),
        ]);
    }

    /**
     * Membangun tabel daftar keluarga.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_kk')
                    ->label('No. KK')
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('No. KK disalin!'),
                TextColumn::make('kepalaKeluarga.nama_lengkap')
                    ->label('Kepala Keluarga'),
                TextColumn::make('dusun')
                    ->label('Dusun'),
                TextColumn::make('rt_rw')
                    ->label('RT/RW'),
                TextColumn::make('anggota_count')
                    ->counts('anggota')
                    ->label('Anggota')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->actionsColumnLabel('Aksi')
            ->defaultSort('no_kk')
            ->striped()
            ->emptyStateHeading('Belum Ada Data Keluarga')
            ->emptyStateIcon('heroicon-o-home-modern');
    }

    /**
     * Query dengan eager load relasi kepala keluarga.
     */
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['kepalaKeluarga']);
    }

    /**
     * Mengembalikan daftar halaman yang tersedia untuk resource ini.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKeluarga::route('/'),
            'create' => Pages\CreateKeluarga::route('/create'),
            'edit' => Pages\EditKeluarga::route('/{record}/edit'),
        ];
    }
}
