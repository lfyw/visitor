<?php

namespace App\Exports;

use App\Models\PassingLog;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;

class PassingLogsExport implements FromArray
{
    use Exportable;

    protected $searchBuilder;

    public function __construct()
    {
        $this->searchBuilder = PassingLog::latest('id');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $data[] = [
            '访客姓名', '人员/访客类型', '性别', '年龄', '身份证号', '手机号', '所属单位', '被访者姓名', '被访者部门', '访问事由', '访客关系', '通行通道', '通行闸机', '闸机方向', '通行时间'
        ];
        $passingLogs = $this->searchBuilder->with([
            'gate.passageways',
            'visitor.visitorType',
            'visitor.user.userType',
        ])->get();
        foreach ($passingLogs as $passingLog){
            $data[] = [
                $passingLog->name,
                $passingLog->type,
                $passingLog->gender,
                $passingLog->age,
                "'" . sm4decrypt($passingLog->id_card),
                "'" . sm4decrypt($passingLog->phone),
                $passingLog->unit,
                $passingLog->user_name,
                $passingLog->user_department,
                $passingLog->reason,
                $passingLog->relation,
                implode(',', $passingLog->gate?->passageways?->pluck('name')->toArray()),
                $passingLog->gate->ip . '-'. $passingLog->gate->number,
                $passingLog->gate->rule,
                $passingLog->passed_at
            ];
        }
        return $data;
    }

    public function searcher(array $searchers)
    {
        $this->searchBuilder->whenIdCard($searchers['id_card'] ?? null)
            ->whenName($searchers['name'] ?? null)
            ->whenType($searchers['type'] ?? null)
            ->whenPassagewayId($searchers['passageway_id'] ?? null)
            ->whenGateId($searchers['gate_id'] ?? null)
            ->whenRule($searchers['rule'] ?? null)
            ->whenPassedAtFrom($searchers['passed_at_from'] ?? now()->subDays(3)->toDateString())
            ->whenPassedAtTo($searchers['passed_at_to'] ?? null)
            ->when($searchers['ids'] ?? null, fn(Builder $builder) => $builder->whereIn('id', $searchers['ids']));
        return $this;
    }
}
