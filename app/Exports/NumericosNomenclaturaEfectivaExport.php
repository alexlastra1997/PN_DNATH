<?php


namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SimpleArraySheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{
    protected string $title;
    protected array $headings;
    protected array $rows;

    public function __construct(string $title, array $headings, array $rows)
    {
        $this->title = $title;
        $this->headings = $headings;
        $this->rows = $rows;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function array(): array
    {
        return $this->rows;
    }
}
