<?php

namespace App\Http\Resources\Pc;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PassingLogResource extends JsonResource
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
            'name' => $this->name,
            'type' => $this->type,
            'gender' => $this->gender,
            'age' => $this->age,
            'id_card' => sm4decrypt($this->id_card),
            'phone' => sm4decrypt($this->phone),
            'unit' => $this->unit,
            'user_department' => $this->user_department,
            'user_name' => $this->user_name,
            'relation' => $this->relation,
            'reason' => $this->reason,
            'gate' => [
                'id' => $this->gate->id,
                'number' => $this->gate->number,
                'ip' => $this->gate->ip,
                'rule' => $this->gate->rule,
                'passageway' => $this->gate->passageways
            ],
            'passed_at' => (string)$this->passed_at,
            'snapshot' => Str::after(Storage::disk('snapshot')->url($this->snapshot), config('app.url'))
        ];
    }

}
