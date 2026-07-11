<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages\ManageAuditLogs;
use App\Models\AuditLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Resource Filament untuk memantau catatan log aktivitas audit (Audit Trail) sistem.
 */
class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $recordTitleAttribute = 'tindakan';

    public static function getGloballySearchableAttributes(): array
    {
        return ['tindakan', 'nama_tabel', 'user_type'];
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-finger-print';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Audit Log';

    protected static ?int $navigationSort = 9;

    /**
     * Audit log bersifat read-only; tidak ada tombol buat data baru.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * Membangun form detail audit log (read-only).
     *
     * Menampilkan metadata aktivitas (waktu, pelaku, IP, user-agent)
     * dan diff perubahan data sebelum vs sesudah dalam format tabel.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('Informasi Log')
                ->description('Metadata aktivitas yang tercatat.')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    \Filament\Forms\Components\Placeholder::make('created_at')
                        ->label('Waktu Kejadian')
                        ->content(fn ($record) => $record->created_at?->format('l, d F Y H:i:s') . ' (' . ($record->created_at?->diffForHumans() ?? '-') . ')'),
                    \Filament\Forms\Components\Placeholder::make('pelaku')
                        ->label('Pelaku / User')
                        ->content(function ($record) {
                            if ($record->user_type === 'admin') {
                                $admin = \App\Models\Administrator::find($record->user_id);
                                return $admin ? "Admin — {$admin->username} (ID: {$record->user_id})" : "Admin (ID: {$record->user_id})";
                            } elseif ($record->user_type === 'warga') {
                                $warga = \App\Models\Penduduk::find($record->user_id);
                                return $warga ? "Warga — {$warga->nama_lengkap} (NIK: {$record->user_id})" : "Warga (NIK: {$record->user_id})";
                            }
                            return 'Sistem / Guest';
                        }),
                    \Filament\Forms\Components\Placeholder::make('tindakan')
                        ->label('Tindakan')
                        ->content(fn ($record) => strtoupper($record->tindakan)),
                    \Filament\Forms\Components\Placeholder::make('nama_tabel')
                        ->label('Target Tabel')
                        ->content(fn ($record) => $record->nama_tabel),
                    \Filament\Forms\Components\Placeholder::make('record_id')
                        ->label('Target Record ID')
                        ->content(fn ($record) => $record->record_id ?? '-'),
                    \Filament\Forms\Components\Placeholder::make('ip_address')
                        ->label('IP Address')
                        ->content(fn ($record) => $record->ip_address ?? '-'),
                    \Filament\Forms\Components\Placeholder::make('user_agent')
                        ->label('User Agent (Browser/Device)')
                        ->content(fn ($record) => $record->user_agent ?? '-')
                        ->columnSpanFull(),
                ])->columns(2),

            \Filament\Schemas\Components\Section::make('Detail Perubahan Data (Diff)')
                ->description('Membandingkan data sebelum (Data Lama) dan sesudah (Data Baru) tindakan.')
                ->icon('heroicon-o-arrows-right-left')
                ->schema([
                    \Filament\Forms\Components\Placeholder::make('diff_viewer')
                        ->label('')
                        ->content(function ($record) {
                            $old = $record->data_lama ?? [];
                            $new = $record->data_baru ?? [];
                            
                            if (empty($old) && empty($new)) {
                                return new \Illuminate\Support\HtmlString('<div class="p-4 text-sm text-gray-500 bg-gray-50 rounded-lg dark:bg-gray-800 dark:text-gray-400">Tidak ada detail data yang berubah (merupakan log tindakan/koneksi).</div>');
                            }

                            $html = '<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">';
                            $html .= '<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 border-collapse">';
                            $html .= '<thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">';
                            $html .= '<tr><th class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">Kolom</th>';
                            $html .= '<th class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400">Data Lama (Sebelum)</th>';
                            $html .= '<th class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400">Data Baru (Sesudah)</th></tr></thead><tbody>';

                            $keys = array_unique(array_merge(array_keys($old), array_keys($new)));
                            $hasChanges = false;
                            
                            foreach ($keys as $key) {
                                if (in_array($key, ['password', 'remember_token', 'token'])) continue;

                                $oldVal = $old[$key] ?? null;
                                $newVal = $new[$key] ?? null;

                                if ($oldVal === $newVal) continue;
                                $hasChanges = true;

                                $oldStr = is_array($oldVal) ? json_encode($oldVal) : (is_bool($oldVal) ? ($oldVal ? 'True' : 'False') : (string)$oldVal);
                                $newStr = is_array($newVal) ? json_encode($newVal) : (is_bool($newVal) ? ($newVal ? 'True' : 'False') : (string)$newVal);

                                $html .= '<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750">';
                                $html .= '<td class="px-4 py-2 font-mono text-xs text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-700"><b>' . htmlspecialchars($key) . '</b></td>';
                                $html .= '<td class="px-4 py-2 border-r border-gray-200 dark:border-gray-700 text-red-600 bg-red-50/20 dark:bg-red-950/5 line-through font-mono text-xs">' . htmlspecialchars($oldStr ?? '-') . '</td>';
                                $html .= '<td class="px-4 py-2 text-green-650 dark:text-green-400 bg-green-50/20 dark:bg-green-950/5 font-mono text-xs font-semibold">' . htmlspecialchars($newStr ?? '-') . '</td>';
                                $html .= '</tr>';
                            }

                            if (!$hasChanges) {
                                return new \Illuminate\Support\HtmlString('<div class="p-4 text-sm text-gray-500 bg-gray-50 rounded-lg dark:bg-gray-800 dark:text-gray-400">Tidak ada modifikasi nilai kolom yang tercatat.</div>');
                            }

                            $html .= '</tbody></table></div>';
                            return new \Illuminate\Support\HtmlString($html);
                        })
                        ->columnSpanFull(),
                ])
        ]);
    }

    /**
     * Membangun tabel daftar log audit.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at?->format('l, d F Y H:i:s')),
                TextColumn::make('user_type')
                    ->label('Tipe User')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'warning',
                        'warga' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('pelaku')
                    ->label('Pelaku')
                    ->getStateUsing(function ($record) {
                        if ($record->user_type === 'admin') {
                            $admin = \App\Models\Administrator::find($record->user_id);
                            return $admin ? "Admin ({$admin->username})" : "Admin (ID: {$record->user_id})";
                        } elseif ($record->user_type === 'warga') {
                            $warga = \App\Models\Penduduk::find($record->user_id);
                            return $warga ? "Warga ({$warga->nama_lengkap})" : "Warga (NIK: {$record->user_id})";
                        }
                        return 'Sistem / Guest';
                    })
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('tindakan')
                    ->label('Tindakan')
                    ->weight('bold')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'create', 'created' => 'success',
                        'update', 'updated' => 'info',
                        'delete', 'deleted' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('nama_tabel')
                    ->label('Tabel')
                    ->color('gray'),
                TextColumn::make('record_id')
                    ->label('Record ID')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->color('gray')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('user_type')
                    ->label('Tipe User')
                    ->options(['admin' => 'Admin', 'warga' => 'Warga'])
                    ->indicator('User'),
                SelectFilter::make('tindakan')
                    ->label('Tindakan')
                    ->options(fn () => AuditLog::query()->distinct()->pluck('tindakan', 'tindakan')->all())
                    ->indicator('Tindakan'),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->poll('15s')
            ->emptyStateHeading('Belum Ada Log Audit')
            ->emptyStateDescription('Aktivitas sistem akan tercatat di sini secara otomatis.')
            ->emptyStateIcon('heroicon-o-finger-print');
    }

    /**
     * Mengembalikan daftar halaman yang tersedia untuk resource ini.
     */
    public static function getPages(): array
    {
        return ['index' => ManageAuditLogs::route('/')];
    }
}
