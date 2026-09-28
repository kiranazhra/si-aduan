<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MediaAduan;
use App\Http\Controllers\Controller;
use App\Models\Aduan;
use App\Models\Kategori;
use App\Models\Unit;
use App\Support\CsvExport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Semua halaman Rekap & Laporan. Angka dihitung langsung dari tabel `aduan`.
 * Filter yang didukung di semua halaman:
 *   ?tahun=2026          (dasar, selalu ada)
 *   ?bulan=11            (opsional - gabung dengan tahun)
 *   ?tanggal=2026-11-15  (opsional - harian, PALING prioritas, menimpa bulan+tahun)
 *   ?unit_id=5           (opsional - kunci ke satu unit/poli; kosong = semua unit)
 *   ?q=kata              (pencarian)
 *   ?export=csv          (unduh Excel/CSV)
 */
class RekapController extends Controller
{
    private const STATUS = ['diproses', 'dikoordinasikan', 'selesai'];

    private const MEDIA_WARNA = [
        'portal'      => '#0f2e5a',
        'whatsapp'    => '#25d366',
        'email'       => '#3b82f6',
        'kotak_saran' => '#f59e0b',
    ];

    private const MEDIA_IKON = [
        'portal'      => 'computer',
        'whatsapp'    => 'chat',
        'email'       => 'email',
        'kotak_saran' => 'inbox',
    ];

    // ─────────────────────────────────────────────────────────
    //  Halaman
    // ─────────────────────────────────────────────────────────

    public function hub(Request $request)
    {
        return view('admin.rekap.hub', $this->umum($request));
    }

    public function data(Request $request)
    {
        $u = $this->umum($request);
        $q = $this->kataCari($request);
        $dasar = fn () => $this->dasar($u['tahun'], $u['bulan'], $u['tanggal'], $u['unitId']);

        $perStatus = $dasar()
            ->selectRaw('status, COUNT(*) AS jumlah')->groupBy('status')->pluck('jumlah', 'status');

        $perBulanDb = $dasar()
            ->selectRaw('MONTH(dibuat_pada) AS bulan, COUNT(*) AS jumlah')->groupBy('bulan')->pluck('jumlah', 'bulan');
        $perBulan = [];
        for ($b = 1; $b <= 12; $b++) {
            $perBulan[] = (int) ($perBulanDb[$b] ?? 0);
        }

        $tabel = $dasar()->with(['kategori', 'lokasi', 'unit'])
            ->when($q !== '', fn (Builder $x) => $this->cariTiket($x, $q))
            ->latest('dibuat_pada')->latest('id');

        if ($this->ekspor($request)) {
            return $this->csvTiket("rekap-data-aduan-{$u['tahun']}.csv", $tabel);
        }

        return view('admin.rekap.data', $u + [
            'q'         => $q,
            'total'     => (int) $perStatus->sum(),
            'perStatus' => [
                'diproses'        => (int) ($perStatus['diproses'] ?? 0),
                'dikoordinasikan' => (int) ($perStatus['dikoordinasikan'] ?? 0),
                'selesai'         => (int) ($perStatus['selesai'] ?? 0),
            ],
            'perBulan'  => $perBulan,
            'tiket'     => (clone $tabel)->paginate(15)->withQueryString(),
            'jumlahSemua' => (int) $perStatus->sum(),
        ]);
    }

    public function kategori(Request $request)
    {
        $u = $this->umum($request);
        $q = $this->kataCari($request);

        $totalAduan = $this->dasar($u['tahun'], $u['bulan'], $u['tanggal'], $u['unitId'])->count();
        $baris = $this->hitungPerGrup(Kategori::query(), $u, $q, $totalAduan);

        if ($this->ekspor($request)) {
            return CsvExport::unduh("rekap-kategori-{$u['tahun']}.csv",
                ['Kategori', 'Total', 'Diproses', 'Dikoordinasikan', 'Selesai', '% Total'],
                $baris->map(fn ($r) => [$r->nama, $r->total, $r->diproses, $r->dikoordinasikan, $r->selesai, $r->pct . '%']));
        }

        return view('admin.rekap.kategori', $u + [
            'q'          => $q,
            'baris'      => $baris,
            'totalAduan' => $totalAduan,
            'jumlahKategori' => Kategori::count(),
            'tertinggi'  => (int) ($baris->max('total') ?? 0),
        ]);
    }

    public function lokasi(Request $request)
    {
        $u = $this->umum($request);
        $q = $this->kataCari($request);

        $totalAduan = $this->dasar($u['tahun'], $u['bulan'], $u['tanggal'], $u['unitId'])->count();
        // Lokasi kejadian sekarang dari tabel unit (98 unit/poli), lewat relasi aduanLokasi
        // (aduan.lokasi_id), beda dari relasi aduan() yang dipakai untuk unit penanganan.
        $baris = $this->hitungPerGrup(Unit::query(), $u, $q, $totalAduan, 'aduanLokasi');

        if ($this->ekspor($request)) {
            return CsvExport::unduh("rekap-lokasi-{$u['tahun']}.csv",
                ['Lokasi', 'Total', 'Diproses', 'Dikoordinasikan', 'Selesai', '% Total'],
                $baris->map(fn ($r) => [$r->nama, $r->total, $r->diproses, $r->dikoordinasikan, $r->selesai, $r->pct . '%']));
        }

        $tertinggi = $baris->first();

        return view('admin.rekap.lokasi', $u + [
            'q'               => $q,
            'baris'           => $baris,
            'totalAduan'      => $totalAduan,
            'jumlahLokasi'    => Unit::count(),
            'namaTertinggi'   => ($tertinggi && $tertinggi->total > 0) ? $tertinggi->nama : null,
            'jumlahTertinggi' => (int) ($tertinggi->total ?? 0),
            'belumSelesai'    => $this->dasar($u['tahun'], $u['bulan'], $u['tanggal'], $u['unitId'])
                ->where('status', '!=', 'selesai')->count(),
        ]);
    }

    public function media(Request $request)
    {
        $u = $this->umum($request);
        $q = $this->kataCari($request);

        $db = $this->dasar($u['tahun'], $u['bulan'], $u['tanggal'], $u['unitId'])
            ->selectRaw("media, COUNT(*) AS total, SUM(status = 'selesai') AS selesai")
            ->groupBy('media')->get()->keyBy('media');

        $totalAduan = (int) $db->sum('total');

        $semua = collect(MediaAduan::cases())->map(function (MediaAduan $m) use ($db, $totalAduan) {
            $total = (int) ($db[$m->value]->total ?? 0);

            return (object) [
                'kode'    => $m->value,
                'nama'    => $m->label(),
                'warna'   => self::MEDIA_WARNA[$m->value],
                'ikon'    => self::MEDIA_IKON[$m->value],
                'total'   => $total,
                'selesai' => (int) ($db[$m->value]->selesai ?? 0),
                'pct'     => $totalAduan ? (int) round($total / $totalAduan * 100) : 0,
            ];
        });

        $baris = $q === ''
            ? $semua
            : $semua->filter(fn ($r) => str_contains(mb_strtolower($r->nama), mb_strtolower($q)))->values();

        if ($this->ekspor($request)) {
            return CsvExport::unduh("rekap-media-{$u['tahun']}.csv",
                ['Saluran', 'Total', 'Selesai', '% Total'],
                $baris->map(fn ($r) => [$r->nama, $r->total, $r->selesai, $r->pct . '%']));
        }

        return view('admin.rekap.media', $u + [
            'q'          => $q,
            'baris'      => $baris,
            'semua'      => $semua,
            'totalAduan' => $totalAduan,
        ]);
    }

    /**
     * Rekap penilaian pelayanan (bintang 1-5) dari pelapor.
     * Kartu & sebaran mengikuti semua filter (termasuk unit); tabel per unit membandingkan
     * SEMUA unit, jadi filter unit_id sengaja tidak diterapkan di tabel itu (sama seperti grading).
     */
    public function rating(Request $request)
    {
        $u = $this->umum($request);
        $q = $this->kataCari($request);

        $sebaranDb = $this->dasar($u['tahun'], $u['bulan'], $u['tanggal'], $u['unitId'])
            ->whereNotNull('rating')
            ->selectRaw('rating, COUNT(*) AS jumlah')->groupBy('rating')->pluck('jumlah', 'rating');

        $totalUlasan = (int) $sebaranDb->sum();
        $jumlahBintang = 0;
        foreach ($sebaranDb as $bintang => $jml) {
            $jumlahBintang += (int) $bintang * (int) $jml;
        }
        $rata = $totalUlasan ? round($jumlahBintang / $totalUlasan, 2) : null;

        $sebaran = collect([5, 4, 3, 2, 1])->map(fn (int $n) => (object) [
            'nilai'  => $n,
            'label'  => Aduan::RATING_LABEL[$n],
            'jumlah' => (int) ($sebaranDb[$n] ?? 0),
            'pct'    => $totalUlasan ? (int) round(($sebaranDb[$n] ?? 0) / $totalUlasan * 100) : 0,
        ]);

        $puas = (int) ($sebaranDb[5] ?? 0) + (int) ($sebaranDb[4] ?? 0);
        $persenPuas = $totalUlasan ? (int) round($puas / $totalUlasan * 100) : 0;

        $semua = $this->hitungRatingUnit($u['tahun'], $u['bulan'], $u['tanggal']);
        $baris = $q === ''
            ? $semua
            : $semua->filter(fn ($r) => str_contains(mb_strtolower($r->nama), mb_strtolower($q)))->values();

        if ($this->ekspor($request)) {
            return CsvExport::unduh("rekap-rating-unit-{$u['tahun']}.csv",
                ['Unit', 'Jumlah Ulasan', 'Rata-rata (dari 5)', 'Sangat Puas (5)', 'Puas (4)', 'Cukup Puas (3)', 'Kurang Puas (2)', 'Tidak Puas (1)'],
                $baris->map(fn ($r) => [$r->nama, $r->total, $r->rata, $r->per[5], $r->per[4], $r->per[3], $r->per[2], $r->per[1]]));
        }

        return view('admin.rekap.rating', $u + [
            'q'           => $q,
            'baris'       => $baris,
            'semua'       => $semua,
            'sebaran'     => $sebaran,
            'totalUlasan' => $totalUlasan,
            'rata'        => $rata,
            'persenPuas'  => $persenPuas,
            'totalAduan'  => $this->dasar($u['tahun'], $u['bulan'], $u['tanggal'], $u['unitId'])->count(),
        ]);
    }

    public function grading(Request $request)
    {
        $u = $this->umum($request);
        $q = $this->kataCari($request);

        // Grading membandingkan antar-unit, jadi filter unit_id sengaja TIDAK diterapkan di sini
        // (kalau unit_id aktif, cukup satu baris yang muncul - itu tetap benar, hanya tidak difilter khusus)
        $semua = $this->hitungGradingUnit($u['tahun'], $u['bulan'], $u['tanggal']);
        $baris = $q === ''
            ? $semua
            : $semua->filter(fn ($r) => str_contains(mb_strtolower($r->nama), mb_strtolower($q)))->values();

        if ($this->ekspor($request)) {
            return CsvExport::unduh("rekap-grading-unit-{$u['tahun']}.csv",
                ['Unit', 'Total Aduan', 'Selesai', 'Belum Selesai', 'Waktu Rata-rata (hari)', 'Grade'],
                $baris->map(fn ($r) => [$r->nama, $r->total, $r->selesai, $r->total - $r->selesai,
                    $r->rata_hari === null ? '-' : $r->rata_hari, $r->grade['label']]));
        }

        return view('admin.rekap.grading', $u + [
            'q'      => $q,
            'baris'  => $baris,
            'semua'  => $semua,
            'jumlah' => [
                'baik'   => $baris->where('skor', '>=', 75)->count(),
                'cukup'  => $baris->where('skor', '>=', 50)->where('skor', '<', 75)->count(),
                'kurang' => $baris->where('skor', '<', 50)->count(),
            ],
        ]);
    }

    public function kesimpulan(Request $request)
    {
        $u = $this->umum($request);
        $q = $this->kataCari($request);
        $dasar = fn () => $this->dasar($u['tahun'], $u['bulan'], $u['tanggal'], $u['unitId']);

        $total   = $dasar()->count();
        $selesai = $dasar()->where('status', 'selesai')->count();
        $belum   = $total - $selesai;

        $tabel = $dasar()->with(['kategori', 'lokasi', 'unit'])
            ->when($q !== '', fn (Builder $x) => $this->cariTiket($x, $q))
            ->latest('dibuat_pada')->latest('id');

        if ($this->ekspor($request)) {
            return $this->csvTiket("rekap-kesimpulan-{$u['tahun']}.csv", $tabel, true);
        }

        return view('admin.rekap.kesimpulan', $u + [
            'q'           => $q,
            'total'       => $total,
            'selesai'     => $selesai,
            'belum'       => $belum,
            'persenSelesai' => $total ? (int) round($selesai / $total * 100) : 0,
            'rekomendasi' => $this->buatRekomendasi($u['tahun'], $total),
            'tiket'       => (clone $tabel)->paginate(15)->withQueryString(),
        ]);
    }

    public function berkas(Request $request)
    {
        $u = $this->umum($request);
        $q = $this->kataCari($request);

        $daftar = [
            ['nama' => 'Rekap Data Aduan',        'ket' => 'Seluruh tiket beserta status dan prioritas', 'rute' => 'admin.recap.data'],
            ['nama' => 'Rekap Kategori Aduan',    'ket' => 'Jumlah aduan per kategori pelayanan',        'rute' => 'admin.recap.category'],
            ['nama' => 'Rekap Lokasi / Ruangan',  'ket' => 'Jumlah aduan per lokasi kejadian',           'rute' => 'admin.recap.room'],
            ['nama' => 'Grading Unit',            'ket' => 'Penilaian kinerja setiap unit pelayanan',    'rute' => 'admin.recap.grading'],
            ['nama' => 'Rating Pelayanan',        'ket' => 'Penilaian bintang dari pelapor per unit',    'rute' => 'admin.recap.rating'],
            ['nama' => 'Kesimpulan & Rekomendasi', 'ket' => 'Daftar tiket lengkap beserta waktu selesai', 'rute' => 'admin.recap.conclusion'],
        ];

        if ($q !== '') {
            $daftar = array_values(array_filter($daftar, fn ($d) => str_contains(mb_strtolower($d['nama']), mb_strtolower($q))));
        }

        return view('admin.rekap.berkas', $u + ['q' => $q, 'daftar' => $daftar]);
    }

    /** Isi jendela "Detail" (daftar tiket) — dimuat lewat fetch dari halaman rekap. */
    public function detail(Request $request)
    {
        $tahun   = $this->tahun($request);
        $bulan   = $this->bulan($request);
        $tanggal = $this->tanggal($request);
        $unitId  = $this->unitId($request);
        $by      = (string) $request->query('by', 'semua');
        $nilai   = (string) $request->query('nilai', '');

        $q = $this->dasar($tahun, $bulan, $tanggal, $unitId)->with('kategori');

        switch ($by) {
            case 'semua':
                break;
            case 'belum':
                $q->where('status', '!=', 'selesai');
                break;
            case 'status':
                abort_unless(in_array($nilai, self::STATUS, true), 404);
                $q->where('status', $nilai);
                break;
            case 'kategori':
                $q->where('kategori_id', (int) $nilai);
                break;
            case 'lokasi':
                $q->where('lokasi_id', (int) $nilai);
                break;
            case 'media':
                abort_unless(MediaAduan::tryFrom($nilai) !== null, 404);
                $q->where('media', $nilai);
                break;
            case 'unit':
                $q->where('unit_id', (int) $nilai);
                break;
            case 'rating':
                abort_unless(in_array((int) $nilai, [1, 2, 3, 4, 5], true), 404);
                $q->where('rating', (int) $nilai);
                break;
            case 'ratingunit':
                // nilai 0 = aduan yang belum diteruskan ke unit
                $q->whereNotNull('rating');
                $nilai === '0' ? $q->whereNull('unit_id') : $q->where('unit_id', (int) $nilai);
                break;
            default:
                abort(404);
        }

        $jumlah = (clone $q)->count();
        $rows   = $q->latest('dibuat_pada')->latest('id')->limit(200)->get();

        return response()->json([
            'jumlah' => $jumlah,
            'html'   => view('admin.rekap._detail', [
                'rows'         => $rows,
                'jumlah'       => $jumlah,
                'tampilRating' => in_array($by, ['rating', 'ratingunit'], true),
            ])->render(),
        ]);
    }

    // ─────────────────────────────────────────────────────────
    //  Bantuan - filter tahun / bulan / tanggal / unit
    // ─────────────────────────────────────────────────────────

    private function tahun(Request $request): int
    {
        $t = (int) $request->query('tahun', now()->year);

        return ($t >= 2000 && $t <= 2100) ? $t : now()->year;
    }

    /** Bulan aktif (1-12) atau null jika tidak difilter per bulan. */
    private function bulan(Request $request): ?int
    {
        $b = $request->query('bulan');
        if ($b === null || $b === '') {
            return null;
        }
        $b = (int) $b;

        return ($b >= 1 && $b <= 12) ? $b : null;
    }

    /** Tanggal spesifik (Y-m-d) atau null. Kalau diisi, ini yang paling prioritas. */
    private function tanggal(Request $request): ?string
    {
        $t = $request->query('tanggal');
        if (! $t) {
            return null;
        }
        try {
            return Carbon::createFromFormat('Y-m-d', $t)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /** Unit/Poli aktif (id) atau null jika "Semua Unit". */
    private function unitId(Request $request): ?int
    {
        $u = $request->query('unit_id');

        return $u !== null && $u !== '' ? (int) $u : null;
    }

    /**
     * Rentang tanggal final, dengan prioritas:
     * tanggal (harian) > bulan+tahun > tahun penuh.
     * @return array{0: Carbon, 1: Carbon}
     */
    private function periode(int $tahun, ?int $bulan = null, ?string $tanggal = null): array
    {
        if ($tanggal !== null) {
            $awal = Carbon::createFromFormat('Y-m-d', $tanggal)->startOfDay();

            return [$awal, $awal->copy()->endOfDay()];
        }

        if ($bulan !== null) {
            $awal = Carbon::create($tahun, $bulan, 1, 0, 0, 0);

            return [$awal, $awal->copy()->endOfMonth()];
        }

        $awal = Carbon::create($tahun, 1, 1, 0, 0, 0);

        return [$awal, $awal->copy()->endOfYear()];
    }

    /** Query dasar dengan SEMUA filter (periode + unit) sudah diterapkan. */
    private function dasar(int $tahun, ?int $bulan = null, ?string $tanggal = null, ?int $unitId = null): Builder
    {
        return Aduan::query()
            ->whereBetween('dibuat_pada', $this->periode($tahun, $bulan, $tanggal))
            ->when($unitId !== null, fn (Builder $q) => $q->where('unit_id', $unitId));
    }

    /**
     * @return array{tahun:int, bulan:?int, tanggal:?string, unitId:?int,
     *               daftarTahun:array<int,int>, daftarUnit:\Illuminate\Support\Collection}
     */
    private function umum(Request $request): array
    {
        $ada = Aduan::query()->selectRaw('DISTINCT YEAR(dibuat_pada) AS t')->pluck('t')
            ->push(now()->year)->map(fn ($t) => (int) $t)->unique()->sortDesc()->values()->all();

        return [
            'tahun'       => $this->tahun($request),
            'bulan'       => $this->bulan($request),
            'tanggal'     => $this->tanggal($request),
            'unitId'      => $this->unitId($request),
            'daftarTahun' => $ada,
            'daftarUnit'  => Unit::orderBy('nama')->get(['id', 'nama']),
        ];
    }

    private function kataCari(Request $request): string
    {
        return trim((string) $request->query('q', ''));
    }

    private function ekspor(Request $request): bool
    {
        return $request->query('export') === 'csv';
    }

    private function cariTiket(Builder $x, string $q): Builder
    {
        return $x->where(function ($w) use ($q) {
            $w->where('nomor_tiket', 'like', "%{$q}%")
              ->orWhere('nama_pelapor', 'like', "%{$q}%")
              ->orWhere('judul', 'like', "%{$q}%");
        });
    }

    /**
     * Hitung aduan per Kategori / Unit-Lokasi (semua baris master tetap tampil, walau 0).
     * $relasi: nama relasi yang dipakai untuk withCount — 'aduan' (default, dipakai Kategori
     * dan Unit-sebagai-penanganan) atau 'aduanLokasi' (Unit-sebagai-lokasi-kejadian).
     */
    private function hitungPerGrup(Builder $master, array $u, string $q, int $totalAduan, string $relasi = 'aduan')
    {
        $p = $this->periode($u['tahun'], $u['bulan'], $u['tanggal']);
        $unitId = $u['unitId'];

        return $master
            ->when($q !== '', fn (Builder $x) => $x->where('nama', 'like', "%{$q}%"))
            ->withCount([
                "{$relasi} as total"           => fn ($x) => $x->whereBetween('dibuat_pada', $p)->when($unitId, fn ($y) => $y->where('unit_id', $unitId)),
                "{$relasi} as diproses"        => fn ($x) => $x->whereBetween('dibuat_pada', $p)->when($unitId, fn ($y) => $y->where('unit_id', $unitId))->where('status', 'diproses'),
                "{$relasi} as dikoordinasikan" => fn ($x) => $x->whereBetween('dibuat_pada', $p)->when($unitId, fn ($y) => $y->where('unit_id', $unitId))->where('status', 'dikoordinasikan'),
                "{$relasi} as selesai"         => fn ($x) => $x->whereBetween('dibuat_pada', $p)->when($unitId, fn ($y) => $y->where('unit_id', $unitId))->where('status', 'selesai'),
            ])
            ->get()
            ->each(fn ($r) => $r->pct = $totalAduan ? (int) round($r->total / $totalAduan * 100) : 0)
            ->sortByDesc('total')
            ->values();
    }

    /**
     * Grading unit. Skor 0-100 = 60% tingkat penyelesaian + 40% kecepatan.
     * Kecepatan = 100 dikurangi 10 poin untuk setiap hari rata-rata penyelesaian (minimum 0).
     * Sengaja tidak menerima $unitId - grading membandingkan SEMUA unit satu sama lain.
     */
    private function hitungGradingUnit(int $tahun, ?int $bulan = null, ?string $tanggal = null)
    {
        $p = $this->periode($tahun, $bulan, $tanggal);

        $data = Aduan::query()->whereBetween('dibuat_pada', $p)->whereNotNull('unit_id')
            ->selectRaw("unit_id, COUNT(*) AS total, SUM(status = 'selesai') AS selesai, "
                . "AVG(CASE WHEN status = 'selesai' AND selesai_pada IS NOT NULL "
                . "THEN TIMESTAMPDIFF(HOUR, dibuat_pada, selesai_pada) / 24 END) AS rata_hari")
            ->groupBy('unit_id')->get();

        $nama = Unit::whereIn('id', $data->pluck('unit_id'))->pluck('nama', 'id');

        return $data->map(function ($d) use ($nama) {
            $total     = (int) $d->total;
            $selesai   = (int) $d->selesai;
            $rata      = $d->rata_hari === null ? null : round((float) $d->rata_hari, 1);
            $tingkat   = $total ? $selesai / $total * 100 : 0;
            $kecepatan = $rata === null ? 0 : max(0, 100 - $rata * 10);
            $skor      = (int) round(0.6 * $tingkat + 0.4 * $kecepatan);

            return (object) [
                'unit_id'   => $d->unit_id,
                'nama'      => $nama[$d->unit_id] ?? 'Unit #' . $d->unit_id,
                'total'     => $total,
                'selesai'   => $selesai,
                'rata_hari' => $rata,
                'skor'      => $skor,
                'grade'     => $this->grade($skor),
            ];
        })->sortByDesc('skor')->values();
    }

    /** Rata-rata dan sebaran bintang per unit. Aduan yang belum diteruskan ke unit ditampilkan sebagai satu baris terpisah. */
    private function hitungRatingUnit(int $tahun, ?int $bulan = null, ?string $tanggal = null)
    {
        $data = Aduan::query()
            ->whereBetween('dibuat_pada', $this->periode($tahun, $bulan, $tanggal))
            ->whereNotNull('rating')
            ->selectRaw('unit_id, COUNT(*) AS total, AVG(rating) AS rata, '
                . 'SUM(rating = 5) AS r5, SUM(rating = 4) AS r4, SUM(rating = 3) AS r3, SUM(rating = 2) AS r2, SUM(rating = 1) AS r1')
            ->groupBy('unit_id')->get();

        $nama = Unit::whereIn('id', $data->pluck('unit_id')->filter())->pluck('nama', 'id');

        return $data->map(fn ($d) => (object) [
            'unit_id' => $d->unit_id ?? 0,
            'nama'    => $d->unit_id ? ($nama[$d->unit_id] ?? 'Unit #' . $d->unit_id) : 'Belum diteruskan ke unit',
            'total'   => (int) $d->total,
            'rata'    => round((float) $d->rata, 2),
            'per'     => [5 => (int) $d->r5, 4 => (int) $d->r4, 3 => (int) $d->r3, 2 => (int) $d->r2, 1 => (int) $d->r1],
        ])->sortByDesc('rata')->values();
    }

    /** @return array{label:string, cls:string, bar:string} */
    private function grade(int $skor): array
    {
        return match (true) {
            $skor >= 75 => ['label' => 'Baik',   'cls' => 'bg-emerald-100 text-emerald-700', 'bar' => 'bg-emerald-500'],
            $skor >= 50 => ['label' => 'Cukup',  'cls' => 'bg-amber-100 text-amber-700',     'bar' => 'bg-amber-400'],
            default     => ['label' => 'Kurang', 'cls' => 'bg-red-100 text-red-700',         'bar' => 'bg-red-500'],
        };
    }

    /** Rekomendasi otomatis dari data (bukan teks tetap). */
    private function buatRekomendasi(int $tahun, int $total): array
    {
        if ($total === 0) {
            return [['head' => 'Belum ada data', 'body' => "Belum ada aduan pada tahun {$tahun}. Rekomendasi akan muncul otomatis setelah ada aduan masuk."]];
        }

        $hasil = [];

        // 1. Kategori tertinggi
        $kat = Kategori::query()
            ->withCount(['aduan as total' => fn ($x) => $x->whereBetween('dibuat_pada', $this->periode($tahun))])
            ->get()->sortByDesc('total')->first();
        if ($kat && $kat->total > 0) {
            $pct = (int) round($kat->total / $total * 100);
            $hasil[] = [
                'head' => "Evaluasi layanan pada kategori {$kat->nama}",
                'body' => "Kategori ini mendominasi {$pct}% aduan ({$kat->total} dari {$total} tiket). Tinjau SOP dan jadwalkan pelatihan bagi petugas terkait.",
            ];
        }

        // 2. Unit paling lambat
        $lambat = $this->hitungGradingUnit($tahun)->filter(fn ($r) => $r->rata_hari !== null)->sortByDesc('rata_hari')->first();
        if ($lambat) {
            $hasil[] = [
                'head' => "Percepat penanganan di {$lambat->nama}",
                'body' => "Rata-rata waktu penyelesaian {$lambat->rata_hari} hari, tertinggi di antara unit lain. Evaluasi alur koordinasi dan tindak lanjut.",
            ];
        }

        // 3. Aduan menumpuk > 7 hari
        $lama = $this->dasar($tahun)->where('status', '!=', 'selesai')
            ->where('dibuat_pada', '<', now()->subDays(7))->count();
        if ($lama > 0) {
            $hasil[] = [
                'head' => 'Tindak lanjuti aduan yang belum selesai',
                'body' => "Ada {$lama} aduan yang belum selesai lebih dari 7 hari. Lakukan pengingat ke unit terkait dan eskalasi bila perlu.",
            ];
        }

        // 4. Saluran non-portal
        $nonPortal = $this->dasar($tahun)->where('media', '!=', 'portal')->count();
        if ($nonPortal > 0) {
            $pct = (int) round($nonPortal / $total * 100);
            $hasil[] = [
                'head' => 'Sosialisasikan portal digital ke pasien',
                'body' => "{$nonPortal} aduan ({$pct}%) masuk lewat saluran non-portal (WhatsApp, email, kotak saran). Pasang QR code portal di area strategis agar aduan tercatat dan mudah dilacak.",
            ];
        }

        return $hasil ?: [['head' => 'Kinerja penanganan baik', 'body' => 'Tidak ada temuan yang perlu ditindaklanjuti saat ini.']];
    }

    private function csvTiket(string $namaFile, Builder $tabel, bool $denganWaktu = false)
    {
        $header = ['No. Tiket', 'Pelapor', 'Tanggal', 'Judul', 'Kategori', 'Lokasi', 'Unit', 'Status', 'Grading'];
        if ($denganWaktu) {
            $header[] = 'Waktu Selesai (hari)';
        }

        $baris = $tabel->lazy()->map(function (Aduan $a) use ($denganWaktu) {
            $r = [
                $a->nomor_tiket,
                $a->anonim ? 'Anonim' : $a->nama_pelapor,
                $a->dibuat_pada->locale('id')->translatedFormat('d M Y'),
                $a->judul,
                $a->kategori->nama ?? '-',
                $a->lokasi->nama ?? '-',
                $a->unit->nama ?? '-',
                ucfirst($a->status),
                ucfirst($a->prioritas),
            ];
            if ($denganWaktu) {
                $r[] = $a->lamaPenyelesaianHari() ?? '-';
            }

            return $r;
        });

        return CsvExport::unduh($namaFile, $header, $baris);
    }
}
