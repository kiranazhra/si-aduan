{{-- Bagian tanda tangan (tempat, tanggal, dan nama), hanya tampil saat dicetak/disimpan sebagai PDF --}}
@props(['tempat' => 'Barabai', 'jabatan' => 'Yang mencetak laporan,'])
<div class="ttd-cetak" style="margin-top: 24px; display: flex; justify-content: flex-end;">
    <div style="text-align: center; font-size: 12px; width: 260px; color: #000;">
        <div style="margin-bottom: 4px;">{{ $tempat }}, {{ now()->locale('id')->translatedFormat('j F Y') }}</div>
        <div style="margin-bottom: 64px;">{{ $jabatan }}</div>
        <div style="font-weight: 700; text-decoration: underline;">{{ auth()->user()->name }}</div>
    </div>
</div>
