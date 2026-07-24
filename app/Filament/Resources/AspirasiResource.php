<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AspirasiResource\Pages\ManageAspirasi;
use App\Models\AuditLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AspirasiResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationLabel = 'Aspirasi Warga';

    protected static string|\UnitEnum|null $navigationGroup = 'Layanan Warga';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('tindakan', 'aspirasi')->latest();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detail Aspirasi')
                ->icon('heroicon-o-chat-bubble-bottom-center-text')
                ->schema([
                    \Filament\Forms\Components\Placeholder::make('created_at')
                        ->label('Waktu Dikirim')
                        ->content(fn ($record) => $record->created_at?->format('l, d F Y H:i:s') . ' (' . ($record->created_at?->diffForHumans() ?? '-') . ')'),
                    \Filament\Forms\Components\Placeholder::make('pesan')
                        ->label('Pesan Aspirasi')
                        ->content(function ($record) {
                            $data = $record->data_baru ?? [];
                            $pesan = $data['pesan'] ?? $data['message'] ?? '-';
                            return nl2br(e($pesan));
                        })
                        ->columnSpanFull(),
                    \Filament\Forms\Components\Placeholder::make('ip_address')
                        ->label('IP Pengirim')
                        ->content(fn ($record) => $record->data_baru['ip'] ?? $record->ip_address ?? '-'),
                    \Filament\Forms\Components\Placeholder::make('user_agent')
                        ->label('Browser / Device')
                        ->content(fn ($record) => $record->data_baru['user_agent'] ?? $record->user_agent ?? '-')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at?->format('l, d F Y H:i:s')),
                TextColumn::make('pesan')
                    ->label('Pesan Aspirasi')
                    ->getStateUsing(fn ($record) => $record->data_baru['pesan'] ?? $record->data_baru['message'] ?? '-')
                    ->limit(80)
                    ->searchable(),
                TextColumn::make('ip')
                    ->label('IP')
                    ->getStateUsing(fn ($record) => $record->data_baru['ip'] ?? '-')
                    ->toggleable(),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('Belum Ada Aspirasi')
            ->emptyStateDescription('Aspirasi warga akan muncul di sini setelah dikirim melalui halaman publik.')
            ->emptyStateIcon('heroicon-o-chat-bubble-bottom-center-text');
    }

    public static function getPages(): array
    {
        return ['index' => ManageAspirasi::route('/')];
    }
}
