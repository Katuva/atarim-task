<?php

namespace Tests\Feature;

use App\Models\Url;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Sqids\Sqids;
use Tests\TestCase;

class UrlShortenerTest extends TestCase
{
    use RefreshDatabase;

    public function testItSuccessfullyEncodesAUrl(): void
    {
        // Set known defaults for testing
        Config::set('shortener.driver', 'hash');
        Config::set('shortener.code_length', 6);
        Config::set('shortener.hash.algorithm', 'xxh3');

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
        Config::set('shortener.driver', 'hash');
        Config::set('shortener.code_length', 6);
        Config::set('shortener.hash.algorithm', 'sha256');

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
        Config::set('shortener.driver', 'hash');
        // This is above the maximum length of 22 characters, so forces the code to be returned in full
        Config::set('shortener.code_length', 50);
        Config::set('shortener.hash.algorithm', 'xxh3');

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
        $this->assertEquals(22, strlen($code));
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

    public function testItSuccessfullyEncodesAnSqid(): void
    {
        Config::set('shortener.driver', 'sqid');
        Config::set('shortener.code_length', 6);

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

    public function testItSuccessfullyDecodesAnSqid(): void
    {
        Config::set('shortener.driver', 'sqid');
        Config::set('shortener.code_length', 6);

        $record = Url::factory()->create([
            'url' => 'https://www.example.com',
        ]);

        $sqids = new Sqids(
            Config::get('shortener.sqid.alphabet'),
            Config::get('shortener.code_length'),
        );

        $code = $sqids->encode([$record->id]);

        $response = $this->get("/api/decode/{$code}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'url',
                'code',
            ],
        ]);
    }
}
