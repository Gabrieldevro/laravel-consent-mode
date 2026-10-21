@php
    $config = config('consent-mode');
    $denied = app(\Gabrieldev\ConsentMode\ConsentState::class)->signals([]);
@endphp
{{-- Trebuie pus în <head>, ÎNAINTE de orice etichetă Google: totul pornește refuzat. --}}
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('consent', 'default', @json($denied + ['wait_for_update' => (int) $config['wait_for_update']]));
    (function () {
        var m = document.cookie.match(/(?:^|; ){{ $config['cookie'] }}=([^;]*)/);
        if (!m) return;
        try {
            var saved = JSON.parse(decodeURIComponent(m[1])), map = @json($config['categories']), upd = {};
            Object.keys(map).forEach(function (c) { map[c].forEach(function (k) { upd[k] = saved[c] === true ? 'granted' : 'denied'; }); });
            gtag('consent', 'update', upd);
        } catch (e) {}
    })();
</script>
@if ($config['ga4_id'])
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $config['ga4_id'] }}"></script>
    <script>gtag('js', new Date()); gtag('config', @json($config['ga4_id']));</script>
@endif
