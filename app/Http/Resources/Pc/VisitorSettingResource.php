<?php

namespace App\Http\Resources\Pc;

use Illuminate\Http\Resources\Json\JsonResource;

class VisitorSettingResource extends JsonResource
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
            'visitor_type' => new VisitorTypeResource($this->whenLoaded('visitorType')),
            'apply_period' => $this->apply_period,
            'approver' => $this->approver,
            'visitor_limiter' => $this->visitor_limiter,
            'visitor_relation' => $this->visitor_relation,
            'way_ids' => WayResource::collection($this->whenLoaded('ways')),
        ];
    }
}
