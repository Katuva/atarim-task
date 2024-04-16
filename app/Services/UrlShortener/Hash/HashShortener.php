<?php

namespace App\Services\UrlShortener\Hash;

use App\Services\UrlShortener\UrlShortenerInterface;
use App\Models\Url;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Random\RandomException;
use RuntimeException;

final class HashShortener implements UrlShortenerInterface
{

    /**
     * Encode a URL into a shortened URL.
     *
     * @param string $url
     * @return Url
     * @throws RandomException
     * @throws RuntimeException
     */
    public function encode(string $url): Url
    {
        $count = 0;

        // Loop until we find a unique code.
        do {
            $count++;

            // If we can't find a unique code after X tries, throw an exception. Just in case.
            // We want no infinite loops here.
            if ($count > Config::get('shortener.max_tries')) {
                throw new RuntimeException('Could not generate a unique URL code.');
            }

            $salt = bin2hex(random_bytes(Config::get('shortener.hash.salt_length')));

            // Defaulted using xxh3 in the config to hash the URL as from research it is the fastest hashing
            // algorithm, and we are not concerned with cryptographic security.
            // Also added the salt to the URL for added entropy.
            // Base64 encode the hash and replace the characters that are not URL safe.
            // This is more for the aesthetics rather than adding any real value.
            $code = Str::of(hash(Config::get('shortener.hash.algorithm'), $url . $salt))
                ->toBase64()
                ->replace(['+', '/', '='], ['A', 'a', '']);

            // Make sure we have a long enough code to truncate. Otherwise, we'll just use the whole thing.
            if ($code->length() > Config::get('shortener.code_length')) {
                // Grab the first X characters of the encoded hash. We don't care about the rest, and with the added
                // entropy from the salt, we shouldn't have any collisions.
                $code = $code->take(Config::get('shortener.code_length'));
            }
        } while (Url::where('code', $code)->exists());

        return Url::create([
            'url' => $url,
            'code' => $code,
        ]);
    }

    /**
     * Decode a shortened URL back into the original URL.
     *
     * @param string $code
     * @return Url
     */
    public function decode(string $code): Url
    {
        // The benefit of offloading all the work to the encode method and database is that we can just
        // return the URL from the database. This is a simple and efficient way to decode the URL.
        return Url::where('code', $code)->firstOrFail();
    }
}
