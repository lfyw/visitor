<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\RuleRequest;
use App\Http\Resources\Pc\RuleResource;
use App\Models\Rule;

class RuleController extends Controller
{
    public function index()
    {
        $rule = Rule::firstOrCreate([
            'name' => '预警设置'
        ], [
            'value' => [
                'no_out' => [],
                'scope' => [],
                'board' => []
            ]
        ]);
        return send_data(new RuleResource($rule));
    }

    public function update(Rule $rule, RuleRequest $ruleRequest)
    {
        $rule->fill($ruleRequest->validated())->save();
        return send_data(new RuleResource($rule));
    }

}
