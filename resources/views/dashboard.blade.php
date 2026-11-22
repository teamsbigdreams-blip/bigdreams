<x-app-layout>
    <div class="container-fluid py-4">
        <x-ihsg-chart height="40vh" />


        <x-screener-result :endpoint="route('screeners.index')" />

        <!-- Konten Hasil Screening di bawah sini -->
    </div>
</x-app-layout>