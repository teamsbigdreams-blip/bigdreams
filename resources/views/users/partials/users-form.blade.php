<dialog class="dialog dialog-scrollable" id="usersModal" x-data="userModal()" @open-users-modal.window="openAdd()" @edit-users-modal.window="openEdit($event.detail)">
    <div class="dialog-header">
        <h1 class="dialog-title" x-text="isEdit ? 'Edit User' : 'Tambah User Baru'"></h1>
        <button type="button" class="btn-close" commandfor="usersModal" command="close" aria-label="Close"></button>
    </div>
    <div class="dialog-body">
        <form id="usersForm" @submit.prevent="submitForm()">
            <x-form.input name="name" label="Nama User" x-model="form.name" required />
            <template x-if="errors.name">
                <div class="invalid-feedback d-block" x-text="errors.name"></div>
            </template>

            <x-form.input name="email" label="Email" type="email" x-model="form.email" required />
            <template x-if="errors.email">
                <div class="invalid-feedback d-block" x-text="errors.email"></div>
            </template>

            <x-form.password-strength name="password" label="Password" :toggle="true" x-model="form.password" />
            <template x-if="errors.password">
                <div class="invalid-feedback d-block" x-text="errors.password"></div>
            </template>

            <x-form.password-strength name="password_confirmation" label="Konfirmasi Password" :toggle="true" x-model="form.password_confirmation" />
            <template x-if="errors.password_confirmation">
                <div class="invalid-feedback d-block" x-text="errors.password_confirmation"></div>
            </template>

            <div class="mb-3" x-show="isEdit">
                <label class="form-label">Status</label>
                <div class="form-field">
                    <div class="switch" style="--bs-switch-width: 2.75rem; --bs-switch-indicator-width: 1.5rem;">
                        <input type="checkbox" value="1" id="switchActiveUser" role="switch" switch x-model="form.is_active">
                    </div>
                    <label for="switchActiveUser">Active</label>
                </div>
            </div>
            <div class="mb-3" x-show="isEdit">
                <label class="form-label">Can Login</label>
                <div class="form-field">
                    <div class="switch" style="--bs-switch-width: 2.75rem; --bs-switch-indicator-width: 1.5rem;">
                        <input type="checkbox" value="1" id="switchCanLogin" role="switch" switch x-model="form.can_login">
                    </div>
                    <label for="switchCanLogin">Yes</label>
                </div>
            </div>
        </form>
    </div>

    <div class="dialog-footer">
        <x-button-close commandfor="usersModal" command="close" />
        <button type="button" class="btn btn-subtle theme-primary" @click="submitForm()">Simpan</button>
    </div>
</dialog>

<style>
    #usersForm input[x-model][aria-invalid="true"],
    #usersForm select[x-model][aria-invalid="true"] {
        border-color: var(--bs-danger);
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('userModal', () => ({
            isEdit: false,
            originalForm: {},
            form: {
                id: '',
                name: '',
                nrk: '',
                'email': '',
                password: '',
                password_confirmation: '',
                is_active: true,
                can_login: false
            },
            errors: {},

            openAdd() {
                this.isEdit = false;
                this.form = {
                    id: '',
                    name: '',
                    nrk: '',
                    'email': '',
                    password: '',
                    password_confirmation: '',
                    is_active: true,
                    can_login: false
                };
                this.originalForm = {};
                this.errors = {};
                document.getElementById('usersModal').showModal();
            },

            openEdit(item) {
                this.isEdit = true;
                this.form = {
                    id: item.id,
                    name: item.name,
                    nrk: item.nrk,
                    'email': item.email,
                    password: '',
                    password_confirmation: '',
                    is_active: Boolean(Number(item.is_active)),
                    can_login: Boolean(Number(item.can_login))
                };
                this.originalForm = {
                    ...this.form
                };
                this.errors = {};
                document.getElementById('usersModal').showModal();
            },

            submitForm() {
                this.errors = {};

                const formData = {
                    ...this.form
                };

                const request = this.isEdit ?
                    axios.put(
                        "{{ route('users.update', ['user' => '__ID__']) }}"
                        .replace('__ID__', this.form.id),
                        formData
                    ) :
                    axios.post("{{ route('users.store') }}", formData);

                request
                    .then(response => {
                        document.getElementById('usersModal').close();
                        window.dispatchEvent(new CustomEvent('user-saved'));

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
                                    document.querySelectorAll('#usersForm [x-model]').forEach(el => {
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