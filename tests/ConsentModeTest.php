<?php

namespace Gabrieldev\ConsentMode\Tests;

use Gabrieldev\ConsentMode\ConsentModeServiceProvider;
use Gabrieldev\ConsentMode\ConsentState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Orchestra\Testbench\TestCase;

class ConsentModeTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ConsentModeServiceProvider::class];
    }

    public function test_head_sets_denied_defaults_before_loading_ga4(): void
    {
        config(['consent-mode.ga4_id' => 'G-TEST123']);
        $html = Blade::render('<x-consent-mode::head />');

        $default = strpos($html, "gtag('consent', 'default'");
        $gtagJs = strpos($html, 'googletagmanager.com/gtag/js?id=G-TEST123');

        $this->assertNotFalse($default);
        $this->assertNotFalse($gtagJs);
        $this->assertLessThan($gtagJs, $default, 'Consent default trebuie să fie înainte de gtag.js');
        foreach (['analytics_storage', 'ad_storage', 'ad_user_data', 'ad_personalization'] as $signal) {
            $this->assertStringContainsString("\"$signal\":\"denied\"", $html);
        }
    }

    public function test_head_without_ga4_does_not_load_google(): void
    {
        $html = Blade::render('<x-consent-mode::head />');

        $this->assertStringNotContainsString('googletagmanager.com', $html);
        $this->assertStringContainsString("gtag('consent', 'default'", $html);
    }

    public function test_banner_renders_categories_and_translations(): void
    {
        $this->app->setLocale('ro');
        $html = Blade::render('<x-consent-mode::banner />');

        $this->assertStringContainsString('data-cm-category="analytics"', $html);
        $this->assertStringContainsString('data-cm-category="marketing"', $html);
        $this->assertStringContainsString('Accept toate', $html);
        $this->assertStringContainsString('/politica-privind-cookie-urile', $html);
    }

    public function test_server_side_state_reads_the_cookie(): void
    {
        $state = $this->app->make(ConsentState::class);

        $request = Request::create('/', 'GET', [], ['consent_mode' => rawurlencode(json_encode(['analytics' => true, 'marketing' => false]))]);
        $this->assertSame(['analytics' => true, 'marketing' => false], $state->fromRequest($request));
        $this->assertFalse($state->allows($request, 'marketing'));

        $this->assertSame(['analytics' => false, 'marketing' => false], $state->fromRequest(Request::create('/')));
        $this->assertSame(['analytics' => false, 'marketing' => false], $state->fromRequest(Request::create('/', 'GET', [], ['consent_mode' => 'nu-e-json'])));
    }

    public function test_signals_map_categories_to_consent_mode_keys(): void
    {
        $signals = $this->app->make(ConsentState::class)->signals(['analytics' => true]);

        $this->assertSame([
            'analytics_storage' => 'granted',
            'ad_storage' => 'denied',
            'ad_user_data' => 'denied',
            'ad_personalization' => 'denied',
        ], $signals);
    }
}
