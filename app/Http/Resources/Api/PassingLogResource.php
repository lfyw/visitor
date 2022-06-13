<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PassingLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'gate' => $this->whenLoaded('gate'),
            'passed_at' => (string)$this->passed_at,
            'snapshot' => Str::after(Storage::disk('snapshot')->url($this->snapshot), config('app.url'))
        ];
    }
}
