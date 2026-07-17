<div class="grid gap-4 md:grid-cols-3">
    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Penduduk aktif</p>
        <p class="mt-2 text-3xl font-bold text-teal-700 dark:text-teal-400">{{ $pendudukAktif }}</p>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Warga dengan status tetap</p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pengajuan pending</p>
        <p class="mt-2 text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $pengajuanPending }}</p>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Menunggu verifikasi petugas</p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Informasi terbit</p>
        <p class="mt-2 text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $informasiTerbit }}</p>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Berita dan pengumuman aktif</p>
    </div>
</div>
