<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <x-filament::button type="submit">
                Simpan Konten Halaman Publik
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
