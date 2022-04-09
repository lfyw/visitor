<?php

namespace App\Http\Resources\Pc;

use App\Http\Resources\Api\AuditorResource;
use App\Http\Resources\Api\WayResource;
use App\Http\Resources\FileResource;
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
            'gender' => $this->gender,
            'age' => $this->age,
            'id_card' => $this->id_card,
            'phone' => $this->phone,
            'unit' => $this->unit,
            'reason' => $this->reason,
            'relation' => $this->relation,
            'audit_status' => $this->audit_status,
            'refused_reason' => $this->refused_reason,
            'access_date_from' => $this->access_date_from,
            'access_date_to' => $this->access_date_to,
            'access_time_from' => $this->access_time_from,
            'access_time_to' => $this->access_time_to,
            'limiter' => $this->limiter,
            'user' => $this->whenLoaded('user'),
            'auditors' => AuditorResource::collection($this->whenLoaded('auditors')),
            'visitor_type' => new VisitorTypeResource($this->whenLoaded('visitorType')),
            'ways' => WayResource::collection($this->whenLoaded('ways')),
            'face_pictures' => FileResource::collection($this->whenLoaded('files')),
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
        ];
    }
}
