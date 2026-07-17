<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="AvaraDesa — Portal informasi dan layanan administrasi desa. Akses layanan surat online, informasi publik, dan pantau status pengajuan.">
        <meta name="theme-color" content="#0F766E">

        <link rel="icon" type="image/svg+xml" href="{{ (\App\Models\PengaturanDesa::get('logo_fav') && \Illuminate\Support\Facades\Storage::disk('public')->exists(\App\Models\PengaturanDesa::get('logo_fav'))) ? \Illuminate\Support\Facades\Storage::url(\App\Models\PengaturanDesa::get('logo_fav')) : asset('logo-fav.svg') }}">

        <title inertia>{{ config('app.name', 'AvaraDesa') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="font-sans">
        @inertia
    </body>
</html>
