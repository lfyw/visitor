<?php

namespace App\Http\Resources\Pc;

use Illuminate\Http\Resources\Json\JsonResource;

class BlacklistResource extends JsonResource
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
            'id_card' => $this->id_card,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'reason' => $this->reason,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at
        ];
    }
}
