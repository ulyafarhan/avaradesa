<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengaturanDesaResource\Pages;
use App\Models\PengaturanDesa;
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
 * Resource Filament untuk mengelola data Konfigurasi Pengaturan Desa.
 */
class PengaturanDesaResource extends Resource
{
    protected static ?string $model = PengaturanDesa::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'kunci';

    public static function getGloballySearchableAttributes(): array
    {
        return ['kunci', 'nilai', 'deskripsi'];
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Pengaturan Desa';

    protected static ?int $navigationSort = 8;

    /**
     * Membangun form isian konfigurasi desa.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Konfigurasi Desa')
                ->description('Atur parameter dan nilai konfigurasi sistem desa.')
                ->icon('heroicon-o-adjustments-horizontal')
                ->schema([
                    TextInput::make('kunci')
                        ->label('Kunci Pengaturan')
                        ->required()
                        ->maxLength(50)
                        ->prefixIcon('heroicon-o-key')
                        ->placeholder('Contoh: nama_desa')
                        ->disabled(fn ($context) => $context === 'edit'),
                    Select::make('tipe_data')
                        ->label('Tipe Data')
                        ->options([
                            'string' => 'String',
                            'integer' => 'Integer',
                            'boolean' => 'Boolean',
                            'json' => 'JSON',
                        ])
                        ->required()
                        ->prefixIcon('heroicon-o-variable')
                        ->disabled(fn ($context) => $context === 'edit'),
                    Textarea::make('nilai')
                        ->label('Nilai')
                        ->required()
                        ->columnSpanFull()
                        ->rows(4)
                        ->placeholder('Masukkan nilai pengaturan...')
                        ->helperText(fn ($get) => $get('tipe_data') === 'json' ? 'Perhatian: Nilai harus ditulis dalam format JSON yang valid (contoh: ["Misi 1", "Misi 2"]). Jangan menghapus tanda kurung siku [] atau kutip "".' : null)
                        ->rules([
                            function ($get) {
                                return function ($attribute, $value, $fail) use ($get) {
                                    if ($get('tipe_data') === 'json') {
                                        json_decode($value);
                                        if (json_last_error() !== JSON_ERROR_NONE) {
                                            $fail('Nilai harus berupa format JSON yang valid.');
                                        }
                                    }
                                };
                            }
                        ]),
                    TextInput::make('deskripsi')
                        ->label('Deskripsi')
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->prefixIcon('heroicon-o-information-circle')
                        ->placeholder('Penjelasan singkat tentang pengaturan ini'),
                ])->columns(1)->columnSpanFull(),
        ]);
    }

    /**
     * Membangun tabel daftar konfigurasi desa.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kunci')
                    ->label('Kunci')
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Kunci disalin!'),
                TextColumn::make('tipe_data')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'string' => 'info',
                        'integer' => 'warning',
                        'boolean' => 'success',
                        'json' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('nilai')
                    ->label('Nilai')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->nilai),
                TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(40)
                    ->color('gray')
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->since()
                    ->sortable()
                    ->tooltip(fn ($record) => $record->updated_at?->format('d M Y H:i:s')),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([
                EditAction::make(),
            ])
            ->actionsColumnLabel('Aksi')
            ->striped()
            ->emptyStateHeading('Belum Ada Pengaturan')
            ->emptyStateDescription('Tambahkan konfigurasi desa baru.')
            ->emptyStateIcon('heroicon-o-cog-6-tooth');
    }

    /**
     * Mengembalikan daftar halaman yang tersedia untuk resource ini.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengaturanDesa::route('/'),
            'create' => Pages\CreatePengaturanDesa::route('/create'),
            'edit' => Pages\EditPengaturanDesa::route('/{record}/edit'),
        ];
    }
}
