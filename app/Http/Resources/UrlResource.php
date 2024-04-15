<?php

namespace App\Http\Resources;

use App\Models\Url;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Url */
class UrlResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'url' => $this->url,
            'code' => $this->code,
        ];
    }
}
