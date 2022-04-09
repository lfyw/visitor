<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class UsersImport implements ToCollection
{
    use Importable;

    protected $errors = [];


    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
    }

    protected function setRowsCount($count)
    {
        $this->rowsCount = $count;
    }

    public function getRowsCount(): int
    {
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
        return ['用户名', '姓名', '所属部门', '所属科室', '用户类型', '所属角色', '用户状态', '职务', '身份证号', '手机号'];
    }

    protected function pushError($row, $message = ''): array
    {
        $error = $row->toArray();
        array_push($error, $message);
        array_push($this->errors, $error);
        return $error;
    }
}
