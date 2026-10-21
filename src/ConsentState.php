<?php

namespace Gabrieldev\ConsentMode;

use Illuminate\Http\Request;

/**
 * Citește pe server alegerea vizitatorului (ex. ca să nu încarci un pixel de marketing fără acord).
 */
class ConsentState
{
    public function __construct(private array $config)
    {
    }

    /** @return array<string, bool> ex. ['analytics' => true, 'marketing' => false] */
    public function fromRequest(Request $request): array
    {
        $raw = $request->cookie($this->config['cookie']);
        $saved = is_string($raw) ? json_decode(rawurldecode($raw), true) : null;

        $state = [];
        foreach (array_keys($this->config['categories']) as $category) {
            $state[$category] = is_array($saved) && ($saved[$category] ?? false) === true;
        }

        return $state;
    }

    public function allows(Request $request, string $category): bool
    {
        return $this->fromRequest($request)[$category] ?? false;
    }

    /** Valorile Consent Mode pentru un set de categorii acceptate. */
    public function signals(array $accepted): array
    {
        $signals = [];
        foreach ($this->config['categories'] as $category => $keys) {
            foreach ($keys as $key) {
                $signals[$key] = ($accepted[$category] ?? false) ? 'granted' : 'denied';
            }
        }

        return $signals;
    }
}
