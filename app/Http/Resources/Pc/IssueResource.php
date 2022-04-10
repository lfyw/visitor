<?php

namespace App\Http\Resources\Pc;

use Illuminate\Http\Resources\Json\JsonResource;

class IssueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $passageway = $this->gate->passageways()->first();
        return [
            'id' => $this->id,
            'gate' => [
                'id' => $this->gate->id,
                'number' => $this->gate->number,
                'ip' => $this->gate->ip,
                'passageways' => $this->when($passageway, [
                    'id' => $passageway->id,
                    'name' => $passageway->name
                ])
            ],
            'rule' => $this->rule,
            'issue_status' => $this->issue_status,
            'created_at' => (string)$this->created_at
        ];
    }
}
