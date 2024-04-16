<?php

namespace App\Services\UrlShortener\Sqid;

use App\Models\Url;
use App\Services\UrlShortener\UrlShortenerInterface;
use Illuminate\Support\Facades\Config;
use Sqids\Sqids;

class SqidShortener implements UrlShortenerInterface
{

    public function encode(string $url): Url
    {
        $sqids = new Sqids(
            Config::get('shortener.squid.alphabet'),
            Config::get('shortener.code_length'),
        );

        $record = Url::create([
            'url' => $url,
        ]);

        $record->code = $sqids->encode([1]);

        return $record;
    }

    public function decode(string $code): Url
    {
        $sqids = new Sqids(
            Config::get('shortener.squid.alphabet'),
            Config::get('shortener.code_length'),
        );

        $id = $sqids->decode($code);

        $record = Url::findOrFail($id[0]);
        $record->code = $code;

        return $record;
    }
}
