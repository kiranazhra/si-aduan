{{-- Kotak pilih unit dengan pencarian. Pakai: <x-admin.unit-picker :units="$units" x-model="unitId" name="unit_id" /> --}}
@props(['units', 'name' => null, 'placeholder' => 'Cari unit…'])
<div x-data="{
        units: @js($units),
        q: '', buka: false, pilihan: '',
        get terpilih() { return this.units.find(u => u.id == this.pilihan) || null; },
        get hasil() { const k = this.q.toLowerCase(); return this.units.filter(u => u.nama.toLowerCase().includes(k)); },
        pilih(u) { this.pilihan = u.id; this.q = ''; this.buka = false; }
     }"
     x-modelable="pilihan" {{ $attributes->except('name') }} class="relative">
    @if ($name)
        <input type="hidden" name="{{ $name }}" :value="pilihan">
    @endif
    <input type="text" autocomplete="off" placeholder="{{ $placeholder }}"
           :value="buka ? q : (terpilih ? terpilih.nama : '')"
           @input="q = $event.target.value; buka = true; if (!$event.target.value) pilihan = ''"
           @focus="buka = true; q = ''"
           @blur="setTimeout(() => buka = false, 150)"
           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-navy transition">
    <div x-show="buka && hasil.length" x-cloak
         class="absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg max-h-48 overflow-y-auto">
        <template x-for="u in hasil" :key="u.id">
            <button type="button" @mousedown.prevent="pilih(u)" x-text="u.nama"
                    class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 transition text-slate-700"></button>
        </template>
    </div>
</div>
