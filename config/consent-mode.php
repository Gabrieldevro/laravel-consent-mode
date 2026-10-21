<?php

return [
    // ID-ul Google Analytics 4 (G-XXXX). Dacă e setat, componenta <x-consent-mode::head /> încarcă și gtag.js.
    'ga4_id' => env('CONSENT_GA4_ID'),

    // Numele și durata cookie-ului în care se salvează alegerea (zile).
    'cookie' => 'consent_mode',
    'lifetime_days' => 180,

    // Linkul către politica de cookie-uri, afișat în banner.
    'policy_url' => '/politica-privind-cookie-urile',

    // Categoriile din banner și semnalele Consent Mode v2 controlate de fiecare.
    'categories' => [
        'analytics' => ['analytics_storage'],
        'marketing' => ['ad_storage', 'ad_user_data', 'ad_personalization'],
    ],

    // Așteaptă alegerea înainte ca etichetele Google să trimită date (ms).
    'wait_for_update' => 500,
];
