<?php

namespace App\Http\Controllers;

use App\Http\Resources\UrlResource;
use App\Services\UrlShortener\UrlShortener;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UrlController extends Controller
{
    // Nice thin controller.
    public function encode(Request $request): UrlResource|JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'url' => 'required|url',
            ]);

            $url = $validatedData['url'];

            return new UrlResource(UrlShortener::create()->encode($url));
        } catch (ValidationException $e) {
            return new JsonResponse(['errors' => $e->errors()], 422);
        }
    }

    public function decode(string $code): UrlResource
    {
        return new UrlResource(UrlShortener::create()->decode($code));
    }
}
