@props([
'endpoint' => '',
'moreUrl' => null,
])

@php
$resolvedMoreUrl = $moreUrl ?? route('screeners.index');
@endphp

<div x-data="screenerResultComponent('{{ $endpoint }}')" class="container-fluid py-3">
    <!-- Pembatas Kotak Rectangle Secondary Tanpa Outline -->
    <div class="w-100 bg-secondary border-0 rounded-2 px-3 py-2 my-4">
        <span class="fw-semibold text-dark">Hasil Screening</span>
    </div>

    <x-table-data
        name="screenerResultTable"
        :endpoint="$endpoint"
        :pagination="false">
        <x-slot:thead>
            <th class="py-2 px-3">Screener Result</th>
        </x-slot:thead>

        <x-slot:tbody>
            <template x-if="index < 10">
                <td class="p-0">
                    <div class="d-flex align-items-center justify-content-between gap-2 px-3 py-2">
                        {{-- Kiri: Company Code & Company Name --}}
                        <div class="min-w-0">
                            <div class="fw-bold text-body-emphasis" x-text="item.company?.company_code ?? '-'"></div>
                            <div class="small text-body-secondary text-truncate" x-text="item.company?.company_name ?? '-'"></div>
                        </div>

                        {{-- Kanan: Harga & Persentase (menggantikan HOURLY_DYNAMIC_HH_HL) --}}
                        <div class="text-end text-nowrap">
                            <div class="fw-semibold text-body-emphasis" x-text="formatPrice(item.current_price ?? item.close)"></div>
                            <div
                                class="small fw-semibold"
                                :class="{
                                    'text-success': Number(item.change_pct) > 0,
                                    'text-danger': Number(item.change_pct) < 0,
                                    'text-body-secondary': item.change_pct === null || Number(item.change_pct) === 0
                                }"
                                x-text="formatPct(item.change_pct)"></div>
                        </div>
                    </div>
                </td>
            </template>
        </x-slot:tbody>
    </x-table-data>

    <div class="d-flex justify-content-end mt-2">
        <a href="{{ $resolvedMoreUrl }}" class="btn btn-link btn-sm text-decoration-none fw-semibold px-0">
            Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
</div>

@once
<script>
    function screenerResultComponent(endpoint) {
        return {
            endpoint,

            formatPrice(val) {
                if (val === null || val === undefined || val === '') return '-';
                return Number(val).toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });
            },

            formatPct(val) {
                if (val === null || val === undefined || Number.isNaN(Number(val))) return '0,00%';
                const num = Number(val);
                const sign = num > 0 ? '+' : '';
                return `${sign}${num.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}%`;
            },
        };
    }
</script>
@endonce