<?php

namespace App\Http\Resources\Pc;

use Illuminate\Http\Resources\Json\JsonResource;
use function Symfony\Component\Translation\t;

class WarningResource extends JsonResource
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
            'type' => $this->type,
            'gender' => $this->gender,
            'age' => $this->age,
            'id_card' => $this->id_card,
            'phone' => $this->phone,
            'unit' => $this->unit,
            'user_real_name' => $this->user_real_name,
            'user_department' => $this->department,
            'reason' => $this->reason,
            'access_count' => $this->visitor?->access_count ?: 0,
            'access_date_from' => $this->access_date_from,
            'access_date_to' => $this->access_date_to,
            'access_time_from' => $this->access_time_from,
            'access_time_to' => $this->access_time_to,
            'ways' => $this->ways,
            'limiter' => $this->limiter,
            'relation' => $this->relation,
            'warning_type' => $this->warning_type,
            'warning_at' => (string)$this->warning_at,
            'status' => $this->status ?: '未处置',
            'note' => $this->note,
            'handler' => $this->whenLoaded('handler'),
            'handled_at' => (string)$this->handled_at
        ];
    }
}
