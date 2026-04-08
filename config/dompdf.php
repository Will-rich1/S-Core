<?php

$candidates = [
    env('DOMPDF_PUBLIC_PATH'),
    public_path(),
    base_path('public'),
    $_SERVER['DOCUMENT_ROOT'] ?? null,
    base_path(),
];

$resolvedPublicPath = null;

foreach ($candidates as $candidate) {
    if (!is_string($candidate) || $candidate === '') {
        continue;
    }

    $realPath = realpath($candidate);
    if ($realPath !== false && is_dir($realPath)) {
        $resolvedPublicPath = $realPath;
        break;
    }
}

return [
    'public_path' => $resolvedPublicPath ?: base_path(),
];
