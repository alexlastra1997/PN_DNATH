<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExcelExport implements FromCollection
{
    protected $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function collection()
    {
        return DB::table($this->table)->get();
    }
}

