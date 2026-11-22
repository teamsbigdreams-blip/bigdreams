<x-app-layout>
    <div class="mb-3">
        <x-breadcrumbs :items="Breadcrumbs::generate('transaction-dailies.index')" />
    </div>

    {{-- Header halaman: judul + keterangan di kiri, tombol tambah di kanan --}}
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3" x-data="{}">
        <div>
            <h1 class="h4 fw-bold text-body-emphasis mb-1">Halaman Transaction Daily</h1>
            <div class="form-text">Kelola data Transaction Daily.</div>
        </div>
        <div class="flex-shrink-0">
            @can('transaction-dailies.create')
            <x-button-add x-on:click="$dispatch('open-transaction-dailies-modal')" />
            @endcan
        </div>
    </div>
    <hr>

    <div x-data="transactionDailyPage">
        <x-table-data name="transactionDailiesList" endpoint="{{ route('transaction-dailies.index') }}" :pagination="true" :refreshEvents="['transaction-dailies-saved']">

            {{-- Slot: langsung input search + tombol filter (sesuai komponen x-table-data terbaru) --}}
            <label class="form-control form-control-sm form-adorn flex-grow-1 mb-0">
                <div class="form-adorn-icon" aria-hidden="true">
                    <i class="bi bi-search"></i>
                </div>
                <input type="search" class="form-ghost w-100" placeholder="Cari Transaction Daily ..." x-model.trim="filters.search" @input.debounce.400ms="applyFilters()">
            </label>

            <div class="flex-shrink-0">
                <span class="d-inline-block rounded-3" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Advanced Search">
                    <button class="btn-solid theme-primary btn-sm" type="button" data-bs-toggle="drawer" data-bs-target="#drawerRight" aria-controls="drawerRight"><i class="bi bi-funnel-fill"></i></button>
                </span>
            </div>

            <dialog class="drawer drawer-end" data-bs-backdrop="false" data-bs-scroll="true" tabindex="-1" id="drawerRight" aria-labelledby="drawerRightLabel">
                <div class="drawer-header">
                    <h5 class="drawer-title" id="drawerRightLabel">Advanced Filters</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="drawer" aria-label="Close"></button>
                </div>
                <div class="drawer-body">
                    <x-form.input name="start_date" label="Start Date" placeholder="YYYY-MM-DD" x-model="filters.start_date" icon="bi-calendar-week" />
                    <x-form.input name="end_date" label="End Date" placeholder="YYYY-MM-DD" x-model="filters.end_date" icon="bi-calendar-week" />

                    <x-button-search @click="applyFilters()" />
                </div>
            </dialog>


            <x-slot name="thead" class="text-center">
                <th style="width: 50px;" class="text-center">No</th>
                <th class="text-center">Company Code</th>
                <th class="text-center">Date</th>
                <th class="text-center">Open</th>
                <th class="text-center">High</th>
                <th class="text-center">Low</th>
                <th class="text-center">Close</th>
                <th class="text-center">Volume</th>

            </x-slot>

            <x-slot name="tbody">
                <td class="text-center" x-text="(items.from || 1) + index"></td>
                <td x-text="item.company?.company_code"></td>
                <td x-text="item.date" class="text-center" style="width: 140px;"></td>
                <td x-text="item.open" class="text-center" style="width: 140px;"></td>
                <td x-text="item.high" class="text-center" style="width: 140px;"></td>
                <td x-text="item.low" class="text-center" style="width: 140px;"></td>
                <td x-text="item.close" class="text-center" style="width: 140px;"></td>
                <td x-text="item.volume" class="text-center" style="width: 140px;"></td>
            </x-slot>
        </x-table-data>
        <!-- 
        {{-- Include Modal Form --}}
       
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('transactionDailyPage', () => ({

            }));
        });
    </script>
</x-app-layout>