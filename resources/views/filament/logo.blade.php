<div style="display: flex; align-items: center; gap: 10px;">
    {{-- Landmark icon --}}
    <svg class="text-teal-600 dark:text-teal-400 shrink-0"
         width="26" height="26" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="2"
         stroke-linecap="round" stroke-linejoin="round"
         style="width: 26px; height: 26px; min-width: 26px;">
        <line x1="3" y1="22" x2="21" y2="22"/>
        <line x1="6" y1="18" x2="6" y2="11"/>
        <line x1="10" y1="18" x2="10" y2="11"/>
        <line x1="14" y1="18" x2="14" y2="11"/>
        <line x1="18" y1="18" x2="18" y2="11"/>
        <polygon points="12 2 20 7 4 7"/>
        <line x1="2" y1="22" x2="22" y2="22"/>
    </svg>

    {{-- Brand text --}}
    <div x-data="{}"
         x-show="typeof $store.sidebar !== 'undefined' ? $store.sidebar.isOpen : true"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-x-1"
         x-transition:enter-end="opacity-100 translate-x-0"
         style="white-space: nowrap; line-height: 1;">
        <span class="text-sm font-bold tracking-tight text-slate-800 dark:text-white" style="font-size: 14px; line-height: 1.2;">
            AvaraDesa<span class="text-teal-600 dark:text-teal-400 font-black">Admin</span>
        </span>
        @php
            $namaDesa = \App\Models\PengaturanDesa::get('nama_desa', 'Udeung');
            if (!str_starts_with(strtolower($namaDesa), 'desa') && !str_starts_with(strtolower($namaDesa), 'desa')) {
                $namaDesa = 'Desa ' . $namaDesa;
            }
        @endphp
        <p class="text-[8px] font-semibold tracking-wider text-teal-600/80 dark:text-teal-400/80 uppercase" style="font-size: 8px; line-height: 1; margin: 2px 0 0 0;">
            {{ $namaDesa }}
        </p>
    </div>
</div>
