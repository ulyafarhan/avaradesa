<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <x-filament::button type="submit">
                Simpan Konfigurasi
            </x-filament::button>
            
            @if(App\Models\PengaturanDesa::get('storage_active_disk') === 's3')
                <x-filament::button type="button" color="warning" wire:click="runMigration" wire:loading.attr="disabled">
                    <span wire:loading.remove>Migrasikan Berkas ke Cloud</span>
                    <span wire:loading>Memproses Migrasi...</span>
                </x-filament::button>
            @endif
        </div>
    </form>
</x-filament-panels::page>
