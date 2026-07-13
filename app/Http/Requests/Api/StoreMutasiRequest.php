<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreMutasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof \App\Models\Penduduk;
    }

    public function rules(): array
    {
        return [
            'nik' => 'required|string|size:16|regex:/^\d{16}$/|exists:penduduk,nik',
            'jenis_mutasi' => 'required|in:Kelahiran,Kematian,Kedatangan,Kepindahan',
            'tanggal_mutasi' => 'required|date|before_or_equal:today',
            'keterangan' => 'required|string|min:5|max:2000',
            'dokumen_bukti' => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'nik.exists' => 'NIK tidak ditemukan dalam data penduduk',
            'jenis_mutasi.in' => 'Jenis mutasi tidak valid',
            'tanggal_mutasi.before_or_equal' => 'Tanggal mutasi tidak boleh melebihi hari ini',
        ];
    }
}
