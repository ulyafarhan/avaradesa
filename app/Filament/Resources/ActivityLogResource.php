<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages\ManageActivityLogs;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $recordTitleAttribute = 'description';

    public static function getGloballySearchableAttributes(): array
    {
        return ['description', 'event', 'causer_type', 'subject_type'];
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Log Aktivitas';

    protected static ?int $navigationSort = 13;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Aktivitas')
                ->description('Detail kejadian aktivitas sistem.')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    \Filament\Forms\Components\Placeholder::make('created_at')
                        ->label('Waktu Kejadian')
                        ->content(fn ($record) => $record->created_at?->format('l, d F Y H:i:s') . ' (' . ($record->created_at?->diffForHumans() ?? '-') . ')'),
                    \Filament\Forms\Components\Placeholder::make('event')
                        ->label('Jenis Aktivitas')
                        ->content(fn ($record) => $record->event ?? '-'),
                    \Filament\Forms\Components\Placeholder::make('description')
                        ->label('Deskripsi')
                        ->content(fn ($record) => $record->description),
                    \Filament\Forms\Components\Placeholder::make('pelaku')
                        ->label('Pelaku')
                        ->content(function ($record) {
                            if (!$record->causer) {
                                return 'Sistem / Guest';
                            }
                            $type = class_basename($record->causer_type);
                            $name = $record->causer->username ?? $record->causer->nama_lengkap ?? $record->causer->name ?? 'ID:' . $record->causer_id;
                            return "$type — $name";
                        }),
                    \Filament\Forms\Components\Placeholder::make('subject')
                        ->label('Target')
                        ->content(function ($record) {
                            if (!$record->subject) {
                                return '-';
                            }
                            $type = class_basename($record->subject_type);
                            $name = $record->subject->nama_surat ?? $record->subject->judul ?? $record->subject->nama_lengkap ?? $record->subject->nama ?? $record->subject->name ?? '#' . $record->subject_id;
                            return "$type — $name";
                        }),
                    \Filament\Forms\Components\Placeholder::make('ip_address')
                        ->label('IP Address')
                        ->content(fn ($record) => $record->properties['ip'] ?? request()->ip()),
                    \Filament\Forms\Components\Placeholder::make('user_agent')
                        ->label('User Agent')
                        ->content(fn ($record) => $record->properties['user_agent'] ?? '-')
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Data Properties')
                ->description('Informasi tambahan yang tercatat.')
                ->icon('heroicon-o-arrows-right-left')
                ->schema([
                    \Filament\Forms\Components\Placeholder::make('properties_detail')
                        ->label('')
                        ->content(function ($record) {
                            $props = $record->properties->toArray() ?? [];
                            $type = $record->properties['type'] ?? null;
                            $diff = $record->properties['diff'] ?? null;

                            $html = '<div class="space-y-2">';

                            if ($type) {
                                $html .= '<div class="text-sm"><span class="font-semibold">Tipe Data:</span> <code>' . htmlspecialchars($type) . '</code></div>';
                            }

                            if ($diff && is_array($diff)) {
                                $html .= '<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">';
                                $html .= '<table class="w-full text-sm text-left border-collapse">';
                                $html .= '<thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700"><tr>';
                                $html .= '<th class="px-4 py-2 border-b">Kunci</th>';
                                $html .= '<th class="px-4 py-2 border-b bg-red-50 dark:bg-red-950/20 text-red-700">Lama</th>';
                                $html .= '<th class="px-4 py-2 border-b bg-green-50 dark:bg-green-950/20 text-green-700">Baru</th>';
                                $html .= '</tr></thead><tbody>';

                                foreach ($diff as $key => $values) {
                                    $oldVal = $values[0] ?? '-';
                                    $newVal = $values[1] ?? '-';
                                    if ($oldVal === $newVal) continue;
                                    $oldStr = is_array($oldVal) ? json_encode($oldVal) : (string)$oldVal;
                                    $newStr = is_array($newVal) ? json_encode($newVal) : (string)$newVal;
                                    $html .= '<tr class="bg-white border-b dark:bg-gray-800"><td class="px-4 py-2 font-mono text-xs">' . htmlspecialchars($key) . '</td>';
                                    $html .= '<td class="px-4 py-2 text-red-600 line-through font-mono text-xs">' . htmlspecialchars($oldStr) . '</td>';
                                    $html .= '<td class="px-4 py-2 text-green-600 font-mono text-xs font-semibold">' . htmlspecialchars($newStr) . '</td></tr>';
                                }

                                $html .= '</tbody></table></div>';
                            } else {
                                $html .= '<pre class="text-xs bg-gray-50 dark:bg-gray-800 p-3 rounded-lg overflow-x-auto max-h-60">' . htmlspecialchars(json_encode($props, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>';
                            }

                            $html .= '</div>';
                            return new \Illuminate\Support\HtmlString($html);
                        })
                        ->columnSpanFull(),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('15s')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at?->format('l, d F Y H:i:s')),
                TextColumn::make('event')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        str_contains($state, 'error') || str_contains($state, 'failed') => 'danger',
                        str_contains($state, 'login') => 'info',
                        str_contains($state, 'changed') || str_contains($state, 'updated') => 'warning',
                        str_contains($state, 'created') || str_contains($state, 'sent') => 'success',
                        str_contains($state, 'deleted') => 'danger',
                        str_contains($state, 'uploaded') => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description)
                    ->searchable(),
                TextColumn::make('pelaku')
                    ->label('Pelaku')
                    ->getStateUsing(function ($record) {
                        if (!$record->causer) {
                            return 'Sistem';
                        }
                        $name = $record->causer->username ?? $record->causer->nama_lengkap ?? $record->causer->name ?? '#' . $record->causer_id;
                        return $name;
                    })
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('properties.ip')
                    ->label('IP')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->label('Jenis Aktivitas')
                    ->options(fn () => Activity::query()->whereNotNull('event')->distinct()->pluck('event', 'event')->all()),
                SelectFilter::make('causer_type')
                    ->label('Tipe Pelaku')
                    ->options(fn () => Activity::query()->whereNotNull('causer_type')->distinct()->pluck('causer_type', 'causer_type')->map(fn ($t) => class_basename($t))->all()),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('Belum Ada Log Aktivitas')
            ->emptyStateDescription('Aktivitas sistem akan tercatat di sini secara otomatis.')
            ->emptyStateIcon('heroicon-o-document-text');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageActivityLogs::route('/'),
        ];
    }
}
