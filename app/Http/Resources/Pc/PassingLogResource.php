<?php

namespace App\Http\Resources\Pc;

use Illuminate\Http\Resources\Json\JsonResource;

class PassingLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $passageway = $this->gate->passageways->first();
        return [
            'id' => $this->id,
            'id_card' => $this->id_card,
            'gate' => [
                'id' => $this->gate->id,
                'number' => $this->gate->number,
                'ip' => $this->gate->ip,
                'rule' => $this->gate->rule,
                'passageway' => $this->when($passageway, [
                    'id' => $passageway->id,
                    'name' => $passageway->name
                ])
            ],
            'passed_at' => (string)$this->passed_at
        ];
    }
}
