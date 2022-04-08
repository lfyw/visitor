<?php

namespace App\Imports;

use App\Models\Department;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class DepartmentsImport implements ToCollection
{
    use Importable;

    protected $errors = [];

    public function collection(Collection $collection)
    {
        $rows = $collection->skip(3);
        $this->setRowsCount($rows->count());

        $rows->each(function ($row) {
            if (!$row[0]) {
                $this->pushError($row, '部门名称不能为空');
                return;
            }

            //1.先保障部门在系统里
            $department = Department::updateOrCreate([
                'name' => $row[0],
                'address' => $row[2],
            ]);
            //2.如果有科室，再录入科室
            if ($row[1]) {
                Department::updateOrCreate([
                    'name' => $row[1],
                    'parent_id' => $department->id,
                ], [
                    'address' => $row[2]
                ]);
            }
        });
    }

    protected function setRowsCount($count)
    {
        $this->rowsCount = $count;
    }

    public function getRowsCount(): int
    {
        dump($this->rowsCount);
        return $this->rowsCount;
    }

    public function getErrorsCount(): int
    {
        return count($this->errors) ?: 0;
    }

    public function getErrorsWithHeader(): array
    {
        $header = $this->getHeaders();
        return $header ? array_merge([$header], $this->errors) : $this->errors;
    }


    protected function getHeaders(): array
    {
        return ['部门名称', '科室名称', '地址'];
    }

    protected function pushError($row, $message = ''): array
    {
        $error = $row->toArray();
        array_push($error, $message);
        array_push($this->errors, $error);
        return $error;
    }

}
