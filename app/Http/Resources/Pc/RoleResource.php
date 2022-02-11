<?php

namespace App\Http\Resources\Pc;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        dump($this);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'users_count' => $this->users_count,
            'permissions' => PermissionResouce::collection($this->whenLoaded('permissions'))
        ];
    }
}
