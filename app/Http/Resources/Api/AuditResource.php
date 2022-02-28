<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Pc\UserResource;
use App\Http\Resources\Pc\VisitorTypeResource;
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
            'id_card' => $this->id_card,
            'department' => $this->department,
            'user' => new UserResource($this->whenLoaded('user')),
            'visitor_type' => new VisitorTypeResource($this->whenLoaded('visitorType')),
            'access_date_from' => $this->access_date_from,
            'access_date_to' => $this->access_date_to,
            'reason' => $this->reason,
            'relation' => $this->relation,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
        ];
    }
}
