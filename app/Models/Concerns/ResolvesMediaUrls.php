<?php

namespace App\Models\Concerns;

trait ResolvesMediaUrls
{
    protected function resolveMediaUrl(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            if (! $this->shouldResolveUrlFromStorage($value)) {
                return $value;
            }

            $path = (string) parse_url($value, PHP_URL_PATH);
            $normalizedFromUrl = $this->normalizeMediaPath($path);

            if ($normalizedFromUrl !== null) {
                return asset('storage/' . $normalizedFromUrl);
            }

            return $value;
        }

        $normalized = $this->normalizeMediaPath($value);

        return $normalized === null ? $value : asset('storage/' . $normalized);
    }

    private function normalizeMediaPath(string $value): ?string
    {
        $normalized = str_replace('\\', '/', ltrim($value, '/'));

        foreach ([
            'storage/app/public/',
            'app/public/',
            'public/storage/',
            'public/',
            'storage/',
        ] as $prefix) {
            $position = strpos($normalized, $prefix);

            if ($position !== false) {
                $normalized = substr($normalized, $position + strlen($prefix));
                break;
            }
        }

        $normalized = ltrim($normalized, '/');

        return $normalized === '' ? null : $normalized;
    }

    private function shouldResolveUrlFromStorage(string $value): bool
    {
        $path = (string) parse_url($value, PHP_URL_PATH);

        if ($path === '' || $this->normalizeMediaPath($path) === null) {
            return false;
        }

        $urlHost = parse_url($value, PHP_URL_HOST);
        $appUrl = config('app.url');
        $appHost = $appUrl ? parse_url($appUrl, PHP_URL_HOST) : null;

        return $urlHost !== null && $appHost !== null && strcasecmp($urlHost, $appHost) === 0;
    }
}

