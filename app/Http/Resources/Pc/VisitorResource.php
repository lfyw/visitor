<?php

namespace App\Http\Resources\Pc;

use App\Http\Resources\FileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitorResource extends JsonResource
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
            'visitor_type' => $this->whenLoaded('visitorType'),
            'id_card' => $this->id_card,
            'phone' => $this->phone,
            'unit' => $this->unit,
            'reason' => $this->reason,
            'limiter' => $this->limiter,
            'user' => $this->whenLoaded('user'),
            'access_date' => $this->access_date,
            'access_time' => $this->access_time,
            'relation' => $this->relation,
            'gender' => $this->gender,
            'age' => $this->age,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
            'ways' => WayResource::collection($this->whenLoaded('ways')),
            'face_pictures' => FileResource::collection($this->whenLoaded('files'))
        ];
    }
}
