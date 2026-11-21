@props([
'height' => '600px',
'interval' => 'D',
'symbol' => 'IDX:COMPOSITE',
'containerId' => 'tv_ihsg_' . uniqid(),
])

<div {{ $attributes->merge(['class' => 'card border-0 shadow-sm rounded-3']) }}>
    <div class="card-body p-0" x-data="ihsgChart('{{ $containerId }}', '{{ $symbol }}', '{{ $interval }}', '{{ $height }}')">
        <div id="{{ $containerId }}" class="w-100" x-ref="chartContainer"></div>
    </div>
</div>

@once
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('ihsgChart', (containerId, symbol, interval, height) => ({
            init() {
                // Set tinggi langsung via JS sebelum widget di-render agar terbaca akurat
                this.$refs.chartContainer.style.height = height;

                if (!window.TradingView) {
                    const script = document.createElement('script');
                    script.src = 'https://s3.tradingview.com/tv.js';
                    script.async = true;
                    script.onload = () => this.renderChart();
                    document.head.appendChild(script);
                } else {
                    this.renderChart();
                }
            },
            renderChart() {
                new TradingView.widget({
                    "autosize": true,
                    "symbol": symbol,
                    "interval": interval,
                    "timezone": "Asia/Jakarta",
                    "theme": "light",
                    "style": "1",
                    "locale": "id",
                    "toolbar_bg": "#f1f3f6",
                    "enable_publishing": false,
                    "hide_top_toolbar": false,
                    "hide_legend": false,
                    "save_image": false,
                    "container_id": containerId
                });
            }
        }));
    });
</script>
@endonce