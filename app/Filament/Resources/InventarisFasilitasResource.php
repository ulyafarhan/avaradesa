<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventarisFasilitasResource\Pages;
use App\Models\InventarisFasilitas;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InventarisFasilitasResource extends Resource
{
    protected static ?string $model = InventarisFasilitas::class;

    protected static ?string $recordTitleAttribute = 'nama_fasilitas';

    public static function getGloballySearchableAttributes(): array
    {
        return ['nama_fasilitas', 'jenis_fasilitas', 'lokasi'];
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Inventaris Fasilitas';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Data Fasilitas')
                ->description('Informasi dasar fasilitas desa.')
                ->icon('heroicon-o-building-office')
                ->schema([
                    TextInput::make('nama_fasilitas')
                        ->label('Nama Fasilitas')
                        ->required()
                        ->maxLength(150),
                    Select::make('jenis_fasilitas')
                        ->label('Jenis Fasilitas')
                        ->options([
                            'Gedung' => 'Gedung',
                            'Jalan' => 'Jalan',
                            'Jembatan' => 'Jembatan',
                            'Irigasi' => 'Irigasi',
                            'Olahraga' => 'Olahraga',
                            'Ibadah' => 'Ibadah',
                            'Kesehatan' => 'Kesehatan',
                            'Pendidikan' => 'Pendidikan',
                            'Air Bersih' => 'Air Bersih',
                            'Penerangan' => 'Penerangan',
                            'Lainnya' => 'Lainnya',
                        ])
                        ->required(),
                    Textarea::make('deskripsi')
                        ->label('Deskripsi')
                        ->rows(3)
                        ->columnSpanFull(),
                    TextInput::make('lokasi')
                        ->label('Lokasi')
                        ->maxLength(200)
                        ->placeholder('Dusun, RT/RW, atau titik lokasi'),
                    Select::make('kondisi')
                        ->label('Kondisi')
                        ->options([
                            'Baik' => 'Baik',
                            'Rusak Ringan' => 'Rusak Ringan',
                            'Rusak Berat' => 'Rusak Berat',
                        ])
                        ->required(),
                    TextInput::make('tahun_dibangun')
                        ->label('Tahun Dibangun')
                        ->numeric()
                        ->minValue(1900)
                        ->maxValue(now()->year),
                    Select::make('status_penggunaan')
                        ->label('Status Penggunaan')
                        ->options([
                            'Aktif' => 'Aktif',
                            'Tidak Aktif' => 'Tidak Aktif',
                            'Renovasi' => 'Renovasi',
                        ])
                        ->required(),
                    FileUpload::make('foto')
                        ->label('Foto Fasilitas')
                        ->image()
                        ->directory('fasilitas')
                        ->maxSize(2048),
                ])->columns(2)->columnSpanFull(),

            Section::make('Koordinat & Publikasi')
                ->description('Lokasi geografis dan visibilitas di portal publik.')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    TextInput::make('latitude')
                        ->label('Latitude')
                        ->numeric()
                        ->step(0.0000001),
                    TextInput::make('longitude')
                        ->label('Longitude')
                        ->numeric()
                        ->step(0.0000001),
                    Toggle::make('is_publik')
                        ->label('Tampilkan di Portal Publik')
                        ->default(true)
                        ->onIcon('heroicon-m-eye')
                        ->offIcon('heroicon-m-eye-slash')
                        ->onColor('success'),
                ])->columns(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_fasilitas')
                    ->label('Nama Fasilitas')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('jenis_fasilitas')
                    ->label('Jenis')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('kondisi')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Baik' => 'success',
                        'Rusak Ringan' => 'warning',
                        'Rusak Berat' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->searchable(),
                TextColumn::make('tahun_dibangun')
                    ->label('Tahun')
                    ->sortable(),
                IconColumn::make('is_publik')
                    ->label('Publik')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->filters([
                SelectFilter::make('jenis_fasilitas')
                    ->label('Jenis')
                    ->options([
                        'Gedung' => 'Gedung',
                        'Jalan' => 'Jalan',
                        'Jembatan' => 'Jembatan',
                        'Irigasi' => 'Irigasi',
                        'Olahraga' => 'Olahraga',
                        'Ibadah' => 'Ibadah',
                        'Kesehatan' => 'Kesehatan',
                        'Pendidikan' => 'Pendidikan',
                        'Air Bersih' => 'Air Bersih',
                        'Penerangan' => 'Penerangan',
                        'Lainnya' => 'Lainnya',
                    ]),
                SelectFilter::make('kondisi')
                    ->label('Kondisi')
                    ->options([
                        'Baik' => 'Baik',
                        'Rusak Ringan' => 'Rusak Ringan',
                        'Rusak Berat' => 'Rusak Berat',
                    ]),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->actionsColumnLabel('Aksi')
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('Belum Ada Data Fasilitas')
            ->emptyStateDescription('Tambahkan inventaris fasilitas desa melalui tombol di atas.')
            ->emptyStateIcon('heroicon-o-building-office');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageInventarisFasilitas::route('/'),
        ];
    }
}
