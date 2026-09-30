<?php

namespace App\Http\Controllers\Admin;

use App\Enums\KelompokUnit;
use App\Enums\PeranPengguna;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\User;
use App\Support\PesanValidasi;
use App\Support\Wa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KonfigurasiController extends Controller
{
    public function index(Request $request)
    {
        $cari = trim((string) $request->query('cari_unit', ''));

        $unit = Unit::query()
            ->when($cari !== '', function ($x) use ($cari) {
                $x->where(function ($w) use ($cari) {
                    $w->where('nama', 'like', "%{$cari}%")
                      ->orWhere('kode', 'like', "%{$cari}%")
                      ->orWhere('penanggung_jawab', 'like', "%{$cari}%");
                });
            })
            ->orderBy('nama')
            ->paginate(10, ['*'], 'unit_page')
            ->withQueryString();

        $staf = User::with('unit')
            ->orderByRaw("FIELD(peran, 'super_admin', 'operator', 'petugas_unit', 'viewer')")
            ->orderBy('name')
            ->get();

        $daftarUnit = Unit::aktif()->orderBy('nama')->get(['id', 'nama'])
            ->map(fn ($u) => ['id' => $u->id, 'nama' => $u->nama])->values();

        return view('admin.konfigurasi', [
            'unit'       => $unit,
            'cari'       => $cari,
            'jumlahUnit' => Unit::count(),
            'staf'       => $staf,
            'daftarUnit' => $daftarUnit,
            'peran'      => PeranPengguna::cases(),
            'kelompok'   => KelompokUnit::cases(),
            'bolehUbah'  => $request->user()->adalahSuperAdmin(),
        ]);
    }

    // ── Unit ────────────────────────────────────────────────
    public function simpanUnit(Request $request)
    {
        $this->hanyaSuperAdmin($request);

        $data = $request->validate([
            'kode'             => ['required', 'string', 'max:10', 'unique:unit,kode'],
            'nama'             => ['required', 'string', 'max:100', 'unique:unit,nama'],
            'kelompok'         => ['required', Rule::in(array_column(KelompokUnit::cases(), 'value'))],
            'penanggung_jawab' => ['nullable', 'string', 'max:100'],
            'no_wa'            => ['nullable', 'string', 'max:20'],
        ], PesanValidasi::UMUM, $this->namaUnit());

        Unit::create([
            'kode'             => strtoupper(trim($data['kode'])),
            'nama'             => trim($data['nama']),
            'kelompok'         => $data['kelompok'],
            'penanggung_jawab' => $data['penanggung_jawab'] ?? null,
            'no_wa'            => Wa::normalisasi($data['no_wa'] ?? null),
            'aktif'            => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.config')->with('success', 'Unit baru ditambahkan.');
    }

    public function ubahUnit(Request $request, Unit $unit)
    {
        $this->hanyaSuperAdmin($request);

        $data = $request->validate([
            'kode'             => ['required', 'string', 'max:10', Rule::unique('unit', 'kode')->ignore($unit->id)],
            'nama'             => ['required', 'string', 'max:100', Rule::unique('unit', 'nama')->ignore($unit->id)],
            'kelompok'         => ['required', Rule::in(array_column(KelompokUnit::cases(), 'value'))],
            'penanggung_jawab' => ['nullable', 'string', 'max:100'],
            'no_wa'            => ['nullable', 'string', 'max:20'],
        ], PesanValidasi::UMUM, $this->namaUnit());

        $unit->update([
            'kode'             => strtoupper(trim($data['kode'])),
            'nama'             => trim($data['nama']),
            'kelompok'         => $data['kelompok'],
            'penanggung_jawab' => $data['penanggung_jawab'] ?? null,
            'no_wa'            => Wa::normalisasi($data['no_wa'] ?? null),
            'aktif'            => $request->boolean('aktif'),
        ]);

        return redirect()->route('admin.config', $request->only('cari_unit', 'unit_page'))
            ->with('success', 'Data unit diperbarui.');
    }

    // ── Staf ────────────────────────────────────────────────
    public function simpanStaf(Request $request)
    {
        $this->hanyaSuperAdmin($request);

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'peran'    => ['required', Rule::in(array_column(PeranPengguna::cases(), 'value'))],
            'unit_id'  => ['nullable', 'required_if:peran,petugas_unit', Rule::exists('unit', 'id')],
            'no_wa'    => ['nullable', 'string', 'max:20'],
        ], PesanValidasi::UMUM, $this->namaStaf());

        User::create([
            'name'     => trim($data['name']),
            'email'    => strtolower(trim($data['email'])),
            'password' => $data['password'],
            'peran'    => $data['peran'],
            'unit_id'  => $data['peran'] === 'petugas_unit' ? $data['unit_id'] : null,
            'no_wa'    => Wa::normalisasi($data['no_wa'] ?? null),
            'aktif'    => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.config')->with('success', 'Staf baru ditambahkan.');
    }

    public function ubahStaf(Request $request, User $user)
    {
        $this->hanyaSuperAdmin($request);

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'max:100'],
            'peran'    => ['required', Rule::in(array_column(PeranPengguna::cases(), 'value'))],
            'unit_id'  => ['nullable', 'required_if:peran,petugas_unit', Rule::exists('unit', 'id')],
            'no_wa'    => ['nullable', 'string', 'max:20'],
        ], PesanValidasi::UMUM, $this->namaStaf());

        $dirinyaSendiri = $user->id === $request->user()->id;

        $isi = [
            'name'    => trim($data['name']),
            'email'   => strtolower(trim($data['email'])),
            'no_wa'   => Wa::normalisasi($data['no_wa'] ?? null),
            // Akun sendiri: peran dan status aktif tidak boleh diubah (supaya tidak terkunci)
            'peran'   => $dirinyaSendiri ? $user->peran->value : $data['peran'],
            'aktif'   => $dirinyaSendiri ? true : $request->boolean('aktif'),
        ];
        $isi['unit_id'] = $isi['peran'] === 'petugas_unit' ? ($data['unit_id'] ?? $user->unit_id) : null;

        if (! empty($data['password'])) {
            $isi['password'] = $data['password'];
        }

        $user->update($isi);

        return redirect()->route('admin.config')->with('success', 'Data staf diperbarui.');
    }

    // ── Bantuan ─────────────────────────────────────────────
    private function hanyaSuperAdmin(Request $request): void
    {
        abort_unless($request->user()->adalahSuperAdmin(), 403, 'Hanya Super Admin yang dapat mengubah konfigurasi.');
    }

    private function namaUnit(): array
    {
        return [
            'kode' => 'Kode unit', 'nama' => 'Nama unit', 'kelompok' => 'Kelompok',
            'penanggung_jawab' => 'Penanggung jawab', 'no_wa' => 'Nomor WhatsApp',
        ];
    }

    private function namaStaf(): array
    {
        return [
            'name' => 'Nama', 'email' => 'Email', 'password' => 'Kata sandi',
            'peran' => 'Peran', 'unit_id' => 'Unit / Poli', 'no_wa' => 'Nomor WhatsApp',
        ];
    }
}
