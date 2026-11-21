<x-app-layout>
    <div class="mb-3">
        <x-breadcrumbs :items="Breadcrumbs::generate('users.index')" />
    </div>

    {{-- Header halaman: judul + keterangan di kiri, tombol tambah di kanan --}}
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3" x-data="{}">
        <div>
            <h1 class="h4 fw-bold text-body-emphasis mb-1">Halaman User</h1>
            <div class="form-text">Kelola data pengguna, status aktif, dan hak akses login.</div>
        </div>
        <div class="flex-shrink-0">
            <x-button-add x-on:click="$dispatch('open-users-modal')" />
        </div>
    </div>
    <hr>

    <div x-data="userPage">
        <x-table-data name="usersList" endpoint="{{ route('users.index') }}" :pagination="true" :refreshEvents="['user-saved']">

            <label class="form-control form-control-sm form-adorn flex-grow-1 mb-0">
                <div class="form-adorn-icon" aria-hidden="true">
                    <i class="bi bi-search"></i>
                </div>
                <input type="search" class="form-ghost w-100" placeholder="Cari nama user ..." x-model.trim="filters.search" @input.debounce.400ms="applyFilters()">
            </label>

            <span class="d-inline-block rounded-3 flex-shrink-0" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Advanced Search">
                <button class="btn-solid theme-primary btn-sm" type="button" data-bs-toggle="drawer" data-bs-target="#drawerRight" aria-controls="drawerRight"><i class="bi bi-funnel-fill"></i></button>
            </span>

            <dialog class="drawer drawer-end" data-bs-backdrop="false" data-bs-scroll="true" tabindex="-1" id="drawerRight" aria-labelledby="drawerRightLabel">
                <div class="drawer-header">
                    <h5 class="drawer-title" id="drawerRightLabel">Advanced Filters</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="drawer" aria-label="Close"></button>
                </div>
                <div class="drawer-body">
                    <div class="mb-1">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control form-control-sm" id="status" x-model="filters.status">
                            <option value="">Pilih Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <x-button-search @click="applyFilters()" />
                </div>
            </dialog>

            <x-slot name="thead" class="text-center">
                <th style="width: 50px;" class="text-center">No</th>
                <th class="text-center">Nama</th>
                <th style="width: 140px;" class="text-center">Status</th>
                <th style="width: 140px;" class="text-center">Akses</th>
                @canany(['users.update', 'users.delete'])
                <th style="width: 140px;" class="text-center">Aksi</th>
                @endcanany
            </x-slot>

            <x-slot name="tbody">
                <td class="text-center" x-text="(items.from || 1) + index"></td>
                <td x-text="item.name"></td>
                <td class="text-center">
                    <template x-if="item.is_active">
                        <span class="badge badge-subtle theme-success"><i class="bi bi-check-circle-fill text-success fs-5" title="Aktif"></i> Aktif</span>
                    </template>
                    <template x-if="!item.is_active">
                        <span class="badge badge-subtle theme-danger"><i class="bi bi-x-circle-fill text-danger fs-5" title="Non-Aktif"></i> Non-Aktif</span>
                    </template>
                </td>
                <td class="text-center">
                    <template x-if="item.can_login">
                        <span class="badge badge-subtle theme-success"><i class="bi bi-check-circle-fill text-success fs-5" title="Ya"></i> Ya</span>
                    </template>
                    <template x-if="!item.can_login">
                        <span class="badge badge-subtle theme-danger"><i class="bi bi-x-circle-fill text-danger fs-5" title="Tidak"></i> Tidak</span>
                    </template>
                </td>
                @canany(['users.update', 'users.delete'])
                <td class="text-center">
                    <div class="position-relative">
                        <button class="btn btn-sm btn-light border-0 px-2 py-1 shadow-none" type="button" data-bs-toggle="menu" aria-expanded="false">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <div class="menu">
                            @can('users.update')
                            <button type="button" class="menu-item text-warning" @click="editItem(item)">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            @endcan
                            @can('users.delete')
                            <button type="button" class="menu-item text-danger" @click="deleteItem(item.id)">
                                <i class="bi bi-trash3-fill"></i> Hapus
                            </button>
                            @endcan
                        </div>
                    </div>
                </td>
                @endcanany
            </x-slot>
        </x-table-data>

        @include('users.partials.users-form')
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('userPage', () => ({
                editItem(item) {
                    window.dispatchEvent(new CustomEvent('edit-users-modal', {
                        detail: item
                    }));
                },
                deleteItem(id) {
                    if (confirm('Yakin ingin menghapus data ini?')) {
                        axios.delete("{{ route('users.destroy', ['user' => '__ID__']) }}".replace('__ID__', id))
                            .then(response => {
                                window.dispatchEvent(new CustomEvent('user-saved'));
                                alert(response.data.message || 'Data berhasil dihapus.');
                            })
                            .catch(error => {
                                alert(error.response?.data?.message || 'Terjadi kesalahan pada server.');
                            });
                    }
                }
            }));
        });
    </script>
</x-app-layout>