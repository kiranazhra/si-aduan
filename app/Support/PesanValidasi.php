<?php

namespace App\Support;

class PesanValidasi
{
    /** Pesan validasi bahasa Indonesia yang sederhana (hanya untuk aturan yang dipakai di panel admin). */
    public const UMUM = [
        'required'    => ':attribute wajib diisi.',
        'required_if' => ':attribute wajib diisi.',
        'unique'      => ':attribute sudah dipakai.',
        'email'       => 'Format :attribute tidak valid.',
        'min'         => ':attribute minimal :min karakter.',
        'max'         => ':attribute maksimal :max karakter.',
        'exists'      => ':attribute tidak ditemukan.',
        'in'          => ':attribute tidak valid.',
    ];
}
