<?php

declare(strict_types=1);

namespace Butler\Service;

use Illuminate\Support\Facades\File;

class Butler
{
    public static function assetsAreCurrent(): bool
    {
        $publishedManifest = public_path('vendor/butler/manifest.json');
        $butlerManifest = __DIR__ . '/../public/vendor/butler/manifest.json';

        throw_unless(File::exists($publishedManifest), 'Assets are not published.');

        return File::get($publishedManifest) === File::get($butlerManifest);
    }
}
