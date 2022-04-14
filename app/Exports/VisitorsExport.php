<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class VisitorsExport implements FromArray, ShouldAutoSize
{
    use Exportable;

    protected $errors = [];

    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        return $this->getErrors();
    }

    public function setErrors($errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
