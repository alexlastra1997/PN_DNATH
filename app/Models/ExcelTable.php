<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelTable extends Model
{
    use HasFactory;
    protected $fillable = ['table_name', 'columns'];

    protected $casts = [
        'columns' => 'array',
    ];

}
