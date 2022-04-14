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
            'access_count' => $this->access_count,
            'user' => $this->whenLoaded('user'),
            'access_date_from' => $this->access_date_from,
            'access_date_to' => $this->access_date_to,
            'access_time_from' => $this->access_time_from,
            'access_time_to' => $this->access_time_to,
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
