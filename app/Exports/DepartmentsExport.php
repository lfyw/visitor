<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;

class DepartmentsExport implements FromArray
{
    use Exportable;

    protected $errors = [];

    public function array(): array
    {
          return $this->getErrors();
    }

    public function setErrors($errors):self
    {
        $this->errors = $errors;
        return $this;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
