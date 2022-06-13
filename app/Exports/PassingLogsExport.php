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
                "'" . $passingLog->id_card,
                "'" . $passingLog->phone,
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
        foreach ($searchers as $searcher) {
            $this->searchBuilder->whenIdCard($searcher['id_card'] ?? null)
                ->whenName($searcher['name'] ?? null)
                ->whenType($searcher['type'] ?? null)
                ->whenPassagewayId($searcher['passageway_id'] ?? null)
                ->whenGateId($searcher['gate_id'] ?? null)
                ->whenRule($searcher['rule'] ?? null)
                ->whenPassedAtFrom($searcher['passed_at_from'] ?? null)
                ->whenPassedAtTo($searcher['passed_at_to'] ?? null)
                ->when($searcher['ids'] ?? null, fn(Builder $builder) => $builder->whereIn('id', $searcher['ids']));
        }
        return $this;
    }
}
