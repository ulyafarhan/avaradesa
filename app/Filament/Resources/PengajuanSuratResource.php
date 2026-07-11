<?php

namespace App\Filament\Resources;

use App\Events\PengajuanStatusUpdated;
use App\Filament\Resources\PengajuanSuratResource\Pages;
use App\Jobs\GenerateSuratPdfJob;
use App\Jobs\SendStatusWhatsappJob;
use App\Models\PengajuanSurat;
use App\Models\TrackingPengajuanSurat;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

/**
 * Resource Filament untuk mengelola berkas Permohonan Pengajuan Surat warga.
 */
class PengajuanSuratResource extends Resource
{
    protected static ?string $model = PengajuanSurat::class;

    protected static ?string $recordTitleAttribute = 'nomor_registrasi';

    public static function getGloballySearchableAttributes(): array
    {
        return ['nomor_registrasi', 'pemohon.nama_lengkap', 'nik_pemohon'];
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-inbox-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Layanan';

    protected static ?string $navigationLabel = 'Pengajuan Surat';

    protected static ?int $navigationSort = 1;

    /**
     * Menampilkan jumlah pengajuan pending sebagai badge navigasi.
     */
    public static function getNavigationBadge(): ?string
    {
        $count = PengajuanSurat::query()->pending()->count();
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
     * Membangun form isian/edit pengajuan surat.
     *
     * Menampilkan informasi registrasi, pemohon, jenis surat, status,
     * catatan penolakan, dan detail data isian serta dokumen pendukung.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Pengajuan')
                ->description('Detail registrasi dan status pengajuan surat.')
                ->icon('heroicon-o-document-text')
                ->schema([
                    TextInput::make('nomor_registrasi')
                        ->label('No. Registrasi')
                        ->disabled(),
                    TextInput::make('nomor_surat')
                        ->label('No. Surat')
                        ->disabled()
                        ->placeholder('Dibuat otomatis setelah disetujui'),
                    Select::make('nik_pemohon')
                        ->relationship('pemohon', 'nama_lengkap')
                        ->label('Pemohon')
                        ->searchable()
                        ->disabled(),
                    Select::make('kategori_surat_id')
                        ->relationship('kategori', 'nama_surat')
                        ->label('Jenis Surat')
                        ->searchable()
                        ->required(),
                    Select::make('status')
                        ->options([
                            'Pending' => 'Pending',
                            'Diproses' => 'Diproses',
                            'Disetujui' => 'Disetujui',
                            'Ditolak' => 'Ditolak',
                            'Selesai' => 'Selesai',
                        ])
                        ->required(),
                    Textarea::make('catatan_penolakan')
                        ->label('Catatan Penolakan')
                        ->columnSpanFull()
                        ->rows(3),
                    TextInput::make('file_pdf_url')
                        ->label('URL Dokumen PDF')
                        ->columnSpanFull(),
                ])->columns(1)->columnSpanFull(),

            Section::make('Detail Data & Dokumen')
                ->description('Data isian formulir dan berkas pemohon.')
                ->icon('heroicon-o-clipboard-document-list')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Placeholder::make('data_isian_details')
                        ->label('Data Isian Formulir')
                        ->content(function ($record) {
                            if (!$record || empty($record->data_isian)) {
                                return new HtmlString('<p class="text-sm text-gray-500 italic">Tidak ada data isian.</p>');
                            }
                            $html = '<div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">';
                            $html .= '<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">';
                            $html .= '<tbody class="divide-y divide-gray-200 dark:divide-gray-700">';
                            foreach ($record->data_isian as $key => $value) {
                                $html .= sprintf(
                                    '<tr><td class="whitespace-nowrap px-4 py-2 font-medium text-gray-700 dark:text-gray-300 border-r border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 w-1/3">%s</td><td class="px-4 py-2 text-gray-600 dark:text-gray-400">%s</td></tr>',
                                    e(ucwords(str_replace('_', ' ', $key))),
                                    e($value)
                                );
                            }
                            $html .= '</tbody></table></div>';
                            return new HtmlString($html);
                        })->columnSpanFull(),
                    Placeholder::make('file_syarat_links')
                        ->label('File Persyaratan')
                        ->content(function ($record) {
                            if (!$record || empty($record->file_syarat)) {
                                return new HtmlString('<p class="text-sm text-gray-500 italic">Tidak ada dokumen.</p>');
                            }
                            $html = '<div class="space-y-1.5">';
                            foreach ($record->file_syarat as $label => $path) {
                                if (empty($path)) continue;
                                $url = str_starts_with($path, 'http') ? $path : asset('storage/' . $path);
                                $html .= sprintf(
                                    '<div class="flex items-center gap-2 text-sm"><span class="font-medium text-gray-700 dark:text-gray-300">%s:</span> <a href="%s" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline">Buka Berkas ↗</a></div>',
                                    e(ucwords(str_replace('_', ' ', $label))),
                                    e($url)
                                );
                            }
                            $html .= '</div>';
                            return new HtmlString($html);
                        })->columnSpanFull(),
                ])->columnSpanFull(),
        ]);
    }

    /**
     * Membangun tabel daftar pengajuan surat dengan aksi setujui/tolak.
     *
     * Menyediakan polling otomatis, filter status, aksi persetujuan individual
     * dan massal dengan lock-for-update, dispatch GenerateSuratPdfJob,
     * serta pencatatan TrackingPengajuanSurat.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->poll('15s')
            ->columns([
                TextColumn::make('nomor_registrasi')
                    ->label('No. Registrasi')
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Disalin!'),
                TextColumn::make('nomor_surat')
                    ->label('No. Surat')
                    ->sortable()
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('pemohon.nama_lengkap')
                    ->label('Pemohon'),
                TextColumn::make('kategori.nama_surat')
                    ->label('Jenis Surat')
                    ->limit(30),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Diproses' => 'info',
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        'Selesai' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Diproses' => 'Diproses',
                        'Disetujui' => 'Disetujui',
                        'Ditolak' => 'Ditolak',
                        'Selesai' => 'Selesai',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (PengajuanSurat $record): bool => !in_array($record->status, ['Disetujui', 'Selesai'], true))
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pengajuan?')
                    ->modalDescription('Dokumen PDF akan digenerate otomatis.')
                    ->modalSubmitActionLabel('Ya, Setujui')
                    ->action(function (PengajuanSurat $record): void {
                        $admin = auth('admin')->user();
                        \Illuminate\Support\Facades\DB::transaction(function () use ($record, $admin) {
                            $lockedRecord = PengajuanSurat::where('id', $record->id)->lockForUpdate()->firstOrFail();
                            $oldStatus = $lockedRecord->status;
                            PengajuanSurat::withoutEvents(function () use ($lockedRecord, $admin) {
                                $lockedRecord->update([
                                    'status' => 'Disetujui',
                                    'diverifikasi_oleh' => $admin?->id,
                                ]);
                            });
                            TrackingPengajuanSurat::create([
                                'pengajuan_surat_id' => $lockedRecord->id,
                                'status_sebelumnya' => $oldStatus,
                                'status_baru' => 'Disetujui',
                                'keterangan_update' => 'Disetujui oleh ' . ($admin?->username ?? 'admin'),
                                'diupdate_oleh' => $admin?->id,
                            ]);
                            if (app()->runningUnitTests()) {
                                GenerateSuratPdfJob::dispatch($lockedRecord);
                            } else {
                                GenerateSuratPdfJob::dispatchSync($lockedRecord);
                            }
                            PengajuanStatusUpdated::dispatch($lockedRecord, $oldStatus, 'Disetujui');
                            
                            // Kirim Notifikasi WhatsApp jika nomor HP ada
                            if (!empty($lockedRecord->nik_pemohon)) {
                                SendStatusWhatsappJob::dispatch(
                                    $lockedRecord->nik_pemohon,
                                    $lockedRecord->kategori?->nama_surat ?? 'Surat Keterangan',
                                    'Disetujui',
                                    $lockedRecord->nomor_registrasi ?? '-'
                                );
                            }
                        });
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (PengajuanSurat $record): bool => $record->status !== 'Ditolak')
                    ->modalHeading('Tolak Pengajuan?')
                    ->modalSubmitActionLabel('Ya, Tolak')
                    ->schema([
                        Textarea::make('catatan_penolakan')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (PengajuanSurat $record, array $data): void {
                        $admin = auth('admin')->user();
                        \Illuminate\Support\Facades\DB::transaction(function () use ($record, $admin, $data) {
                            $lockedRecord = PengajuanSurat::where('id', $record->id)->lockForUpdate()->firstOrFail();
                            $oldStatus = $lockedRecord->status;
                            PengajuanSurat::withoutEvents(function () use ($lockedRecord, $admin, $data) {
                                $lockedRecord->update([
                                    'status' => 'Ditolak',
                                    'catatan_penolakan' => $data['catatan_penolakan'],
                                    'diverifikasi_oleh' => $admin?->id,
                                ]);
                            });
                            TrackingPengajuanSurat::create([
                                'pengajuan_surat_id' => $lockedRecord->id,
                                'status_sebelumnya' => $oldStatus,
                                'status_baru' => 'Ditolak',
                                'keterangan_update' => $data['catatan_penolakan'],
                                'diupdate_oleh' => $admin?->id,
                            ]);
                            PengajuanStatusUpdated::dispatch($lockedRecord, $oldStatus, 'Ditolak');

                            // Kirim Notifikasi WhatsApp jika nomor HP ada
                            if (!empty($lockedRecord->nik_pemohon)) {
                                SendStatusWhatsappJob::dispatch(
                                    $lockedRecord->nik_pemohon,
                                    $lockedRecord->kategori?->nama_surat ?? 'Surat Keterangan',
                                    'Ditolak',
                                    $lockedRecord->nomor_registrasi ?? '-',
                                    $data['catatan_penolakan']
                                );
                            }
                        });
                    }),
                \Filament\Actions\ViewAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('approveBulk')
                        ->label('Setujui Massal')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->modalHeading('Setujui Semua yang Dipilih?')
                        ->modalSubmitActionLabel('Ya, Setujui Semua')
                        ->action(function (\Illuminate\Support\Collection $records): void {
                            $admin = auth('admin')->user();
                            \Illuminate\Support\Facades\DB::transaction(function () use ($records, $admin) {
                                foreach ($records as $record) {
                                    if (in_array($record->status, ['Disetujui', 'Selesai'], true)) continue;
                                    $lockedRecord = PengajuanSurat::where('id', $record->id)->lockForUpdate()->firstOrFail();
                                    $oldStatus = $lockedRecord->status;
                                    PengajuanSurat::withoutEvents(fn () => $lockedRecord->update([
                                        'status' => 'Disetujui',
                                        'diverifikasi_oleh' => $admin?->id,
                                    ]));
                                    TrackingPengajuanSurat::create([
                                        'pengajuan_surat_id' => $lockedRecord->id,
                                        'status_sebelumnya' => $oldStatus,
                                        'status_baru' => 'Disetujui',
                                        'keterangan_update' => 'Setujui massal oleh ' . ($admin?->username ?? 'admin'),
                                        'diupdate_oleh' => $admin?->id,
                                    ]);
                                    if (app()->runningUnitTests()) {
                                        GenerateSuratPdfJob::dispatch($lockedRecord);
                                    } else {
                                        GenerateSuratPdfJob::dispatchSync($lockedRecord);
                                    }
                                    PengajuanStatusUpdated::dispatch($lockedRecord, $oldStatus, 'Disetujui');

                                    // Kirim Notifikasi WhatsApp jika nomor HP ada
                                    if (!empty($lockedRecord->nik_pemohon)) {
                                        SendStatusWhatsappJob::dispatch(
                                            $lockedRecord->nik_pemohon,
                                            $lockedRecord->kategori?->nama_surat ?? 'Surat Keterangan',
                                            'Disetujui',
                                            $lockedRecord->nomor_registrasi ?? '-'
                                        );
                                    }
                                }
                            });
                        }),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->actionsColumnLabel('Aksi')
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('Belum Ada Pengajuan')
            ->emptyStateDescription('Pengajuan surat dari warga akan muncul di sini.')
            ->emptyStateIcon('heroicon-o-inbox');
    }

    /**
     * Query dengan eager load relasi pemohon dan kategori.
     */
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['pemohon', 'kategori']);
    }

    /**
     * Mengembalikan daftar halaman yang tersedia untuk resource ini.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengajuanSurat::route('/'),
            'edit' => Pages\EditPengajuanSurat::route('/{record}/edit'),
        ];
    }
}
