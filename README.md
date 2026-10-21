<h1 align="center">🍪 laravel-consent-mode</h1>

<p align="center">Banner de cookie-uri conform GDPR pentru Laravel, cu <b>Google Consent Mode v2</b> setat corect: totul refuzat implicit, <b>înainte</b> de orice etichetă Google.<br>
<i>GDPR cookie banner for Laravel with Google Consent Mode v2, denied by default and applied before any Google tag.</i></p>

<p align="center">
<img alt="tests" src="https://github.com/Gabrieldevro/laravel-consent-mode/actions/workflows/tests.yml/badge.svg">
<img alt="Laravel" src="https://img.shields.io/badge/Laravel-11%20%7C%2012-FF2D20?style=flat-square&amp;logo=laravel&amp;logoColor=white">
<img alt="Consent Mode" src="https://img.shields.io/badge/Google-Consent%20Mode%20v2-4285F4?style=flat-square&amp;logo=google&amp;logoColor=white">
<img alt="License" src="https://img.shields.io/badge/license-MIT-blue?style=flat-square">
</p>

---

## Problema pe care o rezolvă

Multe bannere de cookie-uri apar pe ecran, dar Google Analytics pornește oricum în spate și pune cookie-uri (`_ga`, `_gcl_au`) **înainte** ca vizitatorul să aleagă ceva. Asta încalcă GDPR. Am întâlnit exact această situație în auditurile pe care le fac.

Pachetul garantează ordinea corectă:

1. `gtag('consent', 'default', { …: 'denied' })`, primul script din pagină;
2. alegerea salvată se reaplică imediat, pe fiecare pagină;
3. abia apoi se încarcă `gtag.js`.

| | |
|---|---|
| ✅ **Consent Mode v2 complet** | `analytics_storage`, `ad_storage`, `ad_user_data`, `ad_personalization` |
| ✅ **3 opțiuni egale** | Accept toate, Refuz și Personalizează pe categorii |
| ✅ **Redeschidere oricând** | orice link `href="#cookie-settings"` redeschide bannerul (cerință GDPR) |
| ✅ **Verificare pe server** | `ConsentState::allows($request, 'marketing')`, ca să nu încarci un pixel fără acord |
| ✅ **Fără dependențe** | JavaScript simplu, fără jQuery, cu texte în română și engleză |

## Instalare

```bash
composer require gabrieldevro/laravel-consent-mode
```

```dotenv
CONSENT_GA4_ID=G-XXXXXXXXXX   # opțional: pachetul încarcă GA4 în ordinea corectă
```

## Utilizare

În layout:

```blade
<head>
    <x-consent-mode::head />   {{-- primul, înaintea oricărui script Google --}}
    ...
</head>
<body>
    ...
    <x-consent-mode::banner />
</body>
```

În pagina de politică a cookie-urilor:

```html
<a href="#cookie-settings">Schimbă preferințele pentru cookie-uri</a>
```

Pe server:

```php
use Gabrieldev\ConsentMode\ConsentState;

@if (app(ConsentState::class)->allows(request(), 'marketing'))
    {{-- Meta Pixel, TikTok Pixel etc. --}}
@endif
```

În JavaScript poți reacționa la alegere:

```js
document.addEventListener('consent-mode:updated', e => console.log(e.detail)); // { analytics: true, marketing: false }
```

### Personalizare

```bash
php artisan vendor:publish --tag=consent-mode-config   # categorii, nume cookie, durată, link politică
php artisan vendor:publish --tag=consent-mode-views    # designul bannerului
php artisan vendor:publish --tag=consent-mode-lang     # texte
```

## Teste

```bash
composer test
```

Testele verifică următoarele:
- valoarea implicită „refuzat” apare **înainte** de `gtag.js`;
- toate cele 4 semnale Consent Mode sunt prezente;
- bannerul are categoriile și traducerile;
- cookie-ul cu alegerea vizitatorului se citește corect pe server, inclusiv cookie-urile corupte.

---

<p align="center">Făcut de <a href="https://gabrieldev.eu"><b>Gabriel Dev</b></a>. SEO tehnic, GDPR și site-uri Laravel care respectă regulile.</p>
