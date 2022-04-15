<?php

namespace App\Http\Resources\Pc;

use Illuminate\Http\Resources\Json\JsonResource;

class OperationLogResource extends JsonResource
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
            'module' => $this->module,
            'content' => $this->content,
            'operated_ip' => $this->operated_ip,
            'operated_at' => $this->operated_at,
            'user' => $this->whenLoaded('user')
        ];
    }
}
