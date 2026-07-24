<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdministratorResource\Pages;
use App\Models\Administrator;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

/**
 * Resource Filament untuk mengelola akun Administrator (Perangkat Desa).
 */
class AdministratorResource extends Resource
{
    protected static ?string $model = Administrator::class;

    protected static ?string $recordTitleAttribute = 'username';

    public static function getGloballySearchableAttributes(): array
    {
        return ['username', 'role'];
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Administrator';

    protected static ?int $navigationSort = 9;

    /**
     * Membangun form isian akun administrator.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Akun Administrator')
                ->description('Kelola akun dan hak akses administrator sistem.')
                ->icon('heroicon-o-shield-check')
                ->schema([
                    TextInput::make('username')
                        ->label('Username')
                        ->required()
                        ->maxLength(50)
                        ->prefixIcon('heroicon-o-user')
                        ->placeholder('Username untuk login'),
                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->revealable()
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->prefixIcon('heroicon-o-lock-closed')
                        ->placeholder('Minimal 8 karakter'),
                    Select::make('role')
                        ->label('Peran')
                        ->options([
                            'kepala desa' => 'Kepala Desa',
                            'sekdes' => 'Sekretaris Desa',
                            'operator' => 'Operator',
                        ])
                        ->required()
                        ->prefixIcon('heroicon-o-key'),
                ])->columns(1)->columnSpanFull(),
        ]);
    }

    /**
     * Membangun tabel daftar administrator.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('username')
                    ->label('Username')
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('role')
                    ->label('Peran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'kepala desa' => 'warning',
                        'sekdes' => 'info',
                        'operator' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'kepala desa' => 'Kepala Desa',
                        'sekdes' => 'Sekretaris Desa',
                        'operator' => 'Operator',
                        default => ucfirst($state),
                    }),
                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at?->format('l, d F Y H:i:s')),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Peran')
                    ->options(['kepala desa' => 'Kepala Desa', 'sekdes' => 'Sekretaris Desa', 'operator' => 'Operator'])
                    ->indicator('Peran'),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->actionsColumnLabel('Aksi')
            ->striped()
            ->emptyStateHeading('Belum Ada Administrator')
            ->emptyStateDescription('Tambahkan akun administrator baru.')
            ->emptyStateIcon('heroicon-o-shield-check');
    }

    /**
     * Mengembalikan daftar halaman yang tersedia untuk resource ini.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdministrator::route('/'),
            'create' => Pages\CreateAdministrator::route('/create'),
            'edit' => Pages\EditAdministrator::route('/{record}/edit'),
        ];
    }
}

