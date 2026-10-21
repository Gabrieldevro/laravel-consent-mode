@php($config = config('consent-mode'))
<div id="consent-mode-banner" class="cm-banner" role="dialog" aria-live="polite" aria-label="{{ __('consent-mode::banner.title') }}" hidden>
    <div class="cm-inner">
        <p class="cm-text">
            {{ __('consent-mode::banner.text') }}
            <a href="{{ $config['policy_url'] }}">{{ __('consent-mode::banner.policy') }}</a>
        </p>
        <div class="cm-categories" hidden>
            <label><input type="checkbox" checked disabled> {{ __('consent-mode::banner.necessary') }}</label>
            @foreach (array_keys($config['categories']) as $category)
                <label><input type="checkbox" data-cm-category="{{ $category }}"> {{ __("consent-mode::banner.$category") }}</label>
            @endforeach
        </div>
        <div class="cm-actions">
            <button type="button" data-cm="reject">{{ __('consent-mode::banner.reject') }}</button>
            <button type="button" data-cm="customize">{{ __('consent-mode::banner.customize') }}</button>
            <button type="button" data-cm="save" hidden>{{ __('consent-mode::banner.save') }}</button>
            <button type="button" data-cm="accept" class="cm-primary">{{ __('consent-mode::banner.accept') }}</button>
        </div>
    </div>
</div>
<style>
    .cm-banner{position:fixed;left:16px;right:16px;bottom:16px;z-index:9999;max-width:760px;margin:0 auto;background:#111827;color:#f9fafb;border-radius:14px;box-shadow:0 20px 50px rgba(0,0,0,.35);font:14px/1.5 system-ui,sans-serif}
    .cm-inner{padding:18px 20px;display:flex;flex-direction:column;gap:12px}
    .cm-text{margin:0}.cm-text a{color:inherit;text-decoration:underline}
    .cm-categories{display:flex;flex-wrap:wrap;gap:12px}
    .cm-actions{display:flex;flex-wrap:wrap;gap:8px;justify-content:flex-end}
    .cm-actions button{border:1px solid rgba(255,255,255,.3);background:transparent;color:inherit;border-radius:8px;padding:8px 14px;cursor:pointer;font:inherit}
    .cm-actions .cm-primary{background:#f9fafb;color:#111827;border-color:#f9fafb;font-weight:600}
</style>
<script>
(function () {
    var banner = document.getElementById('consent-mode-banner');
    var NAME = @json($config['cookie']), DAYS = {{ (int) $config['lifetime_days'] }}, MAP = @json($config['categories']);
    var hasChoice = document.cookie.split('; ').some(function (c) { return c.indexOf(NAME + '=') === 0; });
    if (!hasChoice) banner.hidden = false;

    function save(choice) {
        var expires = new Date(Date.now() + DAYS * 864e5).toUTCString();
        document.cookie = NAME + '=' + encodeURIComponent(JSON.stringify(choice)) + '; expires=' + expires + '; path=/; SameSite=Lax' + (location.protocol === 'https:' ? '; Secure' : '');
        var upd = {};
        Object.keys(MAP).forEach(function (c) { MAP[c].forEach(function (k) { upd[k] = choice[c] ? 'granted' : 'denied'; }); });
        if (typeof gtag === 'function') gtag('consent', 'update', upd);
        document.dispatchEvent(new CustomEvent('consent-mode:updated', { detail: choice }));
        banner.hidden = true;
    }
    function all(value) { var c = {}; Object.keys(MAP).forEach(function (k) { c[k] = value; }); return c; }

    banner.addEventListener('click', function (e) {
        var action = e.target.getAttribute && e.target.getAttribute('data-cm');
        if (action === 'accept') save(all(true));
        if (action === 'reject') save(all(false));
        if (action === 'customize') {
            banner.querySelector('.cm-categories').hidden = false;
            banner.querySelector('[data-cm=save]').hidden = false;
            e.target.hidden = true;
        }
        if (action === 'save') {
            var c = {};
            banner.querySelectorAll('[data-cm-category]').forEach(function (cb) { c[cb.getAttribute('data-cm-category')] = cb.checked; });
            save(c);
        }
    });

    // Orice link cu href="#cookie-settings" redeschide bannerul.
    document.addEventListener('click', function (e) {
        var link = e.target.closest && e.target.closest('a[href="#cookie-settings"]');
        if (!link) return;
        e.preventDefault();
        banner.hidden = false;
    });
})();
</script>
