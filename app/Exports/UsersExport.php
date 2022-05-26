<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class UsersExport implements FromArray, ShouldAutoSize, WithColumnFormatting
{
    use Exportable;

    protected $errors = [];

    public function array(): array
    {
        return $this->getErrors();
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
        ];
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
