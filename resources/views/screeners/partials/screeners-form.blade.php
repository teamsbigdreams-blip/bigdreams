<dialog class="dialog dialog-scrollable" id="screenersModal" x-data="screenerModal()" @open-screeners-modal.window="openAdd()" @edit-screeners-modal.window="openEdit($event.detail)">
    <div class="dialog-header">
        <h1 class="dialog-title" x-text="isEdit ? 'Edit Screener' : 'Tambah Screener Baru'"></h1>
        <button type="button" class="btn-close" commandfor="screenersModal" command="close" aria-label="Close"></button>
    </div>
    <div class="dialog-body">
        <form id="screenersForm" @submit.prevent="submitForm()">
            <div class="mb-3">
                <label for="name" class="form-label">Nama Screener</label>
                <input type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.name }" id="name" x-model="form.name" required>
                <template x-if="errors.name">
                    <div class="invalid-feedback d-block" x-text="errors.name"></div>
                </template>
            </div>

            <div class="mb-3" x-show="isEdit">
                <label class="form-label">Status</label>
                <div class="form-field">
                    <div class="switch" style="--bs-switch-width: 2.75rem; --bs-switch-indicator-width: 1.5rem;">
                        <input type="checkbox" value="1" id="switchActiveScreener" role="switch" switch x-model="form.is_active">
                    </div>
                    <label for="switchActiveScreener">Active</label>
                </div>
            </div>
        </form>
    </div>

    <div class="dialog-footer">
        <x-button-close commandfor="screenersModal" command="close" />
        <button type="button" class="btn btn-subtle theme-primary" @click="submitForm()">Simpan</button>
    </div>
</dialog>

<style>
    #screenersForm input[x-model][aria-invalid="true"],
    #screenersForm select[x-model][aria-invalid="true"] {
        border-color: var(--bs-danger);
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('screenerModal', () => ({
            isEdit: false,
            originalForm: {},
            form: {
                id: '',
                name: '',
                is_active: true
            },
            errors: {},

            openAdd() {
                this.isEdit = false;
                this.form = {
                    id: '',
                    name: '',
                    is_active: true
                };
                this.originalForm = {};
                this.errors = {};
                document.getElementById('screenersModal').showModal();
            },

            openEdit(item) {
                this.isEdit = true;
                this.form = {
                    id: item.id,
                    name: item.name,
                    is_active: Boolean(Number(item.is_active))
                };
                this.originalForm = {
                    ...this.form
                };
                this.errors = {};
                document.getElementById('screenersModal').showModal();
            },

            submitForm() {
                this.errors = {};

                const formData = { ...this.form };

                const request = this.isEdit ?
                    axios.put(
                        "{{ route('screeners.update', ['screener' => '__ID__']) }}"
                        .replace('__ID__', this.form.id),
                        formData
                    ) :
                    axios.post("{{ route('screeners.store') }}", formData);

                request
                    .then(response => {
                        document.getElementById('screenersModal').close();
                        window.dispatchEvent(new CustomEvent('screeners-saved'));
                        alert(response.data.message || 'Data berhasil disimpan.');
                    })
                    .catch(error => {
                        if (error.response) {
                            if (error.response.status === 422 && error.response.data.errors) {
                                const backendErrors = error.response.data.errors;
                                for (const field in backendErrors) {
                                    this.errors[field] = backendErrors[field][0];
                                }
                                this.$nextTick(() => {
                                    document.querySelectorAll('#screenersForm [x-model]').forEach(el => {
                                        const field = el.getAttribute('x-model').split('.').pop();
                                        if (this.errors[field]) {
                                            el.setAttribute('aria-invalid', 'true');
                                        } else {
                                            el.removeAttribute('aria-invalid');
                                        }
                                    });
                                });
                                alert('Validasi gagal. Periksa kembali isian Anda.');
                            } else if (error.response.status === 403) {
                                alert('Akses Ditolak: Anda tidak memiliki permission untuk melakukan aksi ini.');
                            } else {
                                alert('Kesalahan Server: ' + (error.response.data.message || 'Terjadi kesalahan pada server.'));
                            }
                        } else {
                            alert('Koneksi Gagal: Tidak dapat terhubung ke server.');
                        }
                    });
            }
        }));
    });
</script>