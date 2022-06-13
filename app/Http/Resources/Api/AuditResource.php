<?php

namespace App\Http\Resources\Api;

use App\Models\Auditor;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'id_card' => sm4decrypt($this->id_card),
            'name' => $this->name,
            'reason' => $this->reason,
            'access_date_from' => $this->access_date_from,
            'access_date_to' => $this->access_date_to,
            'access_time_from' => $this->access_time_from,
            'access_time_to' => $this->access_time_to,
            'limiter' => $this->limiter,
            'audit_status' => $this->audit_status,
            'user' => $this->whenLoaded('user'),
            'auditors' => AuditorResource::collection($this->whenLoaded('auditors')),
            'visitor_type' => new VisitorTypeResource($this->whenLoaded('visitorType')),
            'ways' => WayResource::collection($this->whenLoaded('ways')),
            'audit_at' => (string)$this->audit_at,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
        ];
    }
}
