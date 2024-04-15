<?php

namespace App\Services\UrlShortener;

use App\Services\UrlShortener\Hash\HashShortener;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

class UrlShortener
{
    // Factory method to create a new instance of the url shortener. Future proofing for when we have multiple drivers.
    public static function create(): UrlShortenerInterface
    {
        $driver = Config::get('shortener.driver');

        // Can create a new url shortener driver by adding a new case here.
        return match ($driver) {
            'hash' => new HashShortener(),
            default => throw new InvalidArgumentException("Invalid shortener driver: {$driver}"),
        };
    }
}
