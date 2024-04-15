<?php

namespace Tests\Feature;

use App\Models\Url;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class UrlShortenerTest extends TestCase
{
    use RefreshDatabase;

    public function testItSuccessfullyEncodesAUrl(): void
    {
        // Set known defaults for testing
        Config::set('shortener.code_length', 6);
        Config::set('shortener.hash_algorithm', 'xxh3');

        $response = $this->post('/api/encode', [
            'url' => 'https://www.example.com',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'url',
                'code',
            ],
        ]);

        $code = $response->json('data.code');
        $this->assertEquals(6, strlen($code));
    }

    public function testItSuccessfullyEncodesAUrlWithADifferentHashAlgorithm(): void
    {
        Config::set('shortener.code_length', 6);
        Config::set('shortener.hash_algorithm', 'sha256');

        $response = $this->post('/api/encode', [
            'url' => 'https://www.example.com',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'url',
                'code',
            ],
        ]);

        $code = $response->json('data.code');
        $this->assertEquals(6, strlen($code));
    }

    public function testItSuccessfullyEncodesAUrlAndReturnsTheFullCode(): void
    {
        // This is above the maximum length of 24 characters, so forces the code to be returned in full
        Config::set('shortener.code_length', 50);
        Config::set('shortener.hash_algorithm', 'xxh3');

        $response = $this->post('/api/encode', [
            'url' => 'https://www.example.com',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'url',
                'code',
            ],
        ]);

        $code = $response->json('data.code');
        $this->assertEquals(24, strlen($code));
    }

    public function testItFailsToEncodeAnInvalidUrl(): void
    {
        $response = $this->post('/api/encode', [
            'url' => 'invalid',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors' => [
                'url',
            ],
        ]);
    }

    public function testItSuccessfullyDecodesAUrl(): void
    {
        Url::factory()->create([
            'url' => 'https://www.example.com',
            'code' => 'example',
        ]);

        $response = $this->get('/api/decode/example');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'url',
                'code',
            ],
        ]);
    }

    public function testItFailsToDecodeAnInvalidCode(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $response = $this->get('/api/decode/invalid');

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'message',
        ]);
    }
}
