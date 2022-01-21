<?php

namespace App\Http\Resources\Pc;

use Illuminate\Http\Resources\Json\JsonResource;

class WayResource extends JsonResource
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
            'name' => $this->name,
            'note' => $this->note,
            'passageways' => PassagewayResource::collection($this->whenLoaded('passageways'))
        ];
    }
}
