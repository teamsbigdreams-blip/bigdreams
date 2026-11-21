<dialog class="dialog dialog-scrollable" id="companiesModal" x-data="companyModal()" @open-companies-modal.window="openAdd()" @edit-companies-modal.window="openEdit($event.detail)">
    <div class="dialog-header">
        <h1 class="dialog-title" x-text="isEdit ? 'Edit Company' : 'Tambah Company Baru'"></h1>
        <button type="button" class="btn-close" commandfor="companiesModal" command="close" aria-label="Close"></button>
    </div>
    <div class="dialog-body">
        <form id="companiesForm" @submit.prevent="submitForm()">
            <div class="mb-3">
                <label for="company_name" class="form-label">Company Name</label>
                <input type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.company_name }" id="company_name" x-model="form.company_name" required>
                <template x-if="errors.company_name">
                    <div class="invalid-feedback d-block" x-text="errors.company_name"></div>
                </template>
            </div>
            <div class="mb-3">
                <label for="company_code" class="form-label">Company Code</label>
                <input type="text" class="form-control form-control-sm" :class="{ 'is-invalid': errors.company_code }" id="company_code" x-model="form.company_code" required>
                <template x-if="errors.company_code">
                    <div class="invalid-feedback d-block" x-text="errors.company_code"></div>
                </template>
            </div>


        </form>
    </div>

    <div class="dialog-footer">
        <x-button-close commandfor="companiesModal" command="close" />
        <button type="button" class="btn btn-subtle theme-primary" @click="submitForm()">Simpan</button>
    </div>
</dialog>

<style>
    #companiesForm input[x-model][aria-invalid="true"],
    #companiesForm select[x-model][aria-invalid="true"] {
        border-color: var(--bs-danger);
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('companyModal', () => ({
            isEdit: false,
            originalForm: {},
            form: {
                id: '',
                company_code: '',
                company_name: '',

            },
            errors: {},

            openAdd() {
                this.isEdit = false;
                this.form = {
                    id: '',
                    company_code: '',
                    company_name: '',
                };
                this.originalForm = {};
                this.errors = {};
                document.getElementById('companiesModal').showModal();
            },

            openEdit(item) {
                this.isEdit = true;
                this.form = {
                    id: item.id,
                    company_code: item.company_code,
                    company_name: item.company_name,
                };
                this.originalForm = {
                    ...this.form
                };
                this.errors = {};
                document.getElementById('companiesModal').showModal();
            },

            submitForm() {
                this.errors = {};

                const formData = {
                    ...this.form
                };

                const request = this.isEdit ?
                    axios.put(
                        "{{ route('companies.update', ['company' => '__ID__']) }}"
                        .replace('__ID__', this.form.id),
                        formData
                    ) :
                    axios.post("{{ route('companies.store') }}", formData);

                request
                    .then(response => {
                        document.getElementById('companiesModal').close();
                        window.dispatchEvent(new CustomEvent('companies-saved'));
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
                                    document.querySelectorAll('#companiesForm [x-model]').forEach(el => {
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