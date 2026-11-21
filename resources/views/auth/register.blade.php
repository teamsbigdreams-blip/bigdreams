<x-guest-layout title="Daftar">
    <div class="card border shadow-sm rounded-4 overflow-hidden">
        <div class="bg-primary" style="height: 4px;"></div>

        <div class="card-body p-4 p-sm-5">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3" style="width: 56px; height: 56px;">
                    <i class="bi bi-person-plus-fill text-primary fs-4"></i>
                </div>
                <h1 class="h5 fw-bold text-body-emphasis mb-1">Buat Akun Baru</h1>
                <p class="small text-body-secondary mb-0">Daftar menggunakan alamat email Anda</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <x-form.input name="name" label="Nama Lengkap" icon="bi-person" placeholder="Nama lengkap" :value="old('name')" required autocomplete="name" />
                <x-form.input name="email" label="Alamat Email" type="email" icon="bi-envelope" placeholder="nama@contoh.com" :value="old('email')" required autocomplete="email" inputmode="email" />
                <x-form.password name="password" label="Kata Sandi" :toggle="true" required autocomplete="new-password" />
                <x-form.password name="password_confirmation" label="Konfirmasi Kata Sandi" :toggle="true" required autocomplete="new-password" />
                <x-form.submit label="Daftar sekarang" icon="bi-person-plus-fill" />
                <p class="text-center small text-body-secondary mt-4 mb-0">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Masuk</a>
                </p>
            </form>
        </div>
    </div>
</x-guest-layout>