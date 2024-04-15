<?php

return [

    /*
     * The driver to use to generate the short URL.
     */

    'driver' => env('SHORTENER_DRIVER', 'hash'),

    /*
     * The length of the code to generate.
     */

    'code_length' => env('SHORTENER_CODE_LENGTH', 6),

    /*
     * Maximum number of tries to generate a unique code if a collision is found.
     */

    'max_tries' => env('SHORTENER_MAX_TRIES', 100),

    /*
     * The hashing algorithm to use to hash the URL.
     */

    'hash_algorithm' => env('SHORTENER_HASH_ALGORITHM', 'xxh3'),

    /*
     * The length of the salt to add to the URL before hashing.
     */

    'salt_length' => env('SHORTENER_SALT_LENGTH', 16),

];
