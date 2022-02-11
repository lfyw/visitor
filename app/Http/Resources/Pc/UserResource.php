<?php

namespace App\Http\Resources\Pc;

use App\Http\Resources\FileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'real_name' => $this->real_name,
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'user_type' => $this->whenLoaded('userType'),
            'ways' => WayResource::collection($this->whenLoaded('ways')),
            'role' => $this->role,
            'user_status' => $this->user_status,
            'duty' => $this->duty,
            'id_card' => $this->id_card,
            'phone_number' => $this->phone_number,
            'issue_status' => $this->issue_status,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
            'face_pictures' => FileResource::collection($this->whenLoaded('files'))
        ];
    }
}
