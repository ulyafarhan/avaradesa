<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BotKnowledgeResource\Pages;
use App\Models\BotKnowledge;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
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

/**
 * Resource Filament untuk mengelola data basis pengetahuan chatbot Telegram.
 */
class BotKnowledgeResource extends Resource
{
    protected static ?string $model = BotKnowledge::class;

    protected static ?string $recordTitleAttribute = 'pertanyaan_atau_topik';

    public static function getGloballySearchableAttributes(): array
    {
        return ['pertanyaan_atau_topik', 'kunci', 'jawaban_atau_konten'];
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Tanya Jawab Bot Telegram';

    protected static ?int $navigationSort = 9;

    /**
     * Membangun form isian data basis pengetahuan bot.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Panduan & Basis Pengetahuan Bot')
                ->description('Kelola daftar tanya jawab otomatis (FAQ) dan data pendukung untuk kecerdasan buatan (AI) Bot Telegram Desa.')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->schema([
                    TextInput::make('kunci')
                        ->label('Kode Singkat Topik (Tanpa Spasi)')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50)
                        ->prefixIcon('heroicon-o-key')
                        ->placeholder('Contoh: sktm, profil_desa, jam_layanan')
                        ->disabled(fn ($context) => $context === 'edit'),
                    Select::make('tipe')
                        ->label('Jenis Jawaban Bot')
                        ->options([
                            'faq' => 'Tanya Jawab Langsung (FAQ - Jawaban Pasti)',
                            'kb' => 'Informasi Pendukung AI (Konteks Latar Belakang)',
                        ])
                        ->required()
                        ->prefixIcon('heroicon-o-variable'),
                    TextInput::make('pertanyaan_atau_topik')
                        ->label('Pertanyaan / Topik Utama')
                        ->required()
                        ->maxLength(255)
                        ->prefixIcon('heroicon-o-information-circle')
                        ->placeholder('Contoh: Bagaimana syarat pengajuan Surat Keterangan Tidak Mampu (SKTM)?'),
                    TextInput::make('kata_kunci')
                        ->label('Kata Kunci Pencarian (Pisahkan dengan Koma)')
                        ->required()
                        ->placeholder('Contoh: sktm, tidak mampu, bantuan, miskin')
                        ->dehydrateStateUsing(fn ($state) => is_array($state) ? $state : array_map('trim', explode(',', $state)))
                        ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                        ->prefixIcon('heroicon-o-tag')
                        ->helperText('Kata-kata yang sering diketik warga di Telegram untuk memicu jawaban ini.'),
                    Textarea::make('jawaban_atau_konten')
                        ->label('Isi Jawaban / Penjelasan Informasi')
                        ->required()
                        ->rows(6)
                        ->columnSpanFull()
                        ->placeholder('Tulis jawaban langsung yang akan dikirimkan bot ke warga, atau tulis penjelasan detail informasi desa yang akan diolah oleh kecerdasan buatan (AI)...'),
                    Toggle::make('is_aktif')
                        ->label('Aktifkan Respon Ini')
                        ->default(true)
                        ->onIcon('heroicon-m-check')
                        ->offIcon('heroicon-m-x-mark')
                        ->onColor('success'),
                ])->columns(1)->columnSpanFull(),
        ]);
    }

    /**
     * Membangun tabel daftar basis pengetahuan bot.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pertanyaan_atau_topik')
                    ->label('Topik / Pertanyaan Utama')
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('kunci')
                    ->label('Kode Topik')
                    ->sortable()
                    ->copyable(),
                TextColumn::make('tipe')
                    ->label('Jenis Jawaban')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'faq' => 'success',
                        'kb' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'faq' => 'Tanya Jawab Langsung',
                        'kb' => 'Informasi Pendukung AI',
                        default => $state,
                    }),
                IconColumn::make('is_aktif')
                    ->label('Status Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->since()
                    ->sortable(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->actionsColumnLabel('Aksi')
            ->striped();
    }

    /**
     * Mengembalikan daftar halaman yang tersedia untuk resource ini.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBotKnowledge::route('/'),
        ];
    }
}
