<?php

namespace App\Services\UrlShortener;

use App\Models\Url;

interface UrlShortenerInterface
{
    public function encode(string $url): Url;
    public function decode(string $code): Url;
}
