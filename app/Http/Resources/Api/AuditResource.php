<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Pc\UserResource;
use App\Http\Resources\Pc\VisitorTypeResource;
use App\Http\Resources\Pc\WayResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditResource extends JsonResource
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
            'reason' => $this->reason,
            'access_date_from' => $this->access_date_from,
            'access_date_to' => $this->access_date_to,
            'access_time_from' => $this->access_time_from,
            'access_time_to' => $this->access_time_to,
            'access_count' => $this->access_count,
            'limiter' => $this->limiter,
            'user' => $this->whenLoaded('user'),
            'auditors' => AuditorResource::collection($this->whenLoaded('auditors')),
            'visitor_type' => new VisitorTypeResource($this->whenLoaded('visitorType')),
            'ways' => WayResource::collection($this->whenLoaded('ways')),
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
        ];
    }
}