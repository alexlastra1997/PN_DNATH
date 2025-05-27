<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'cargo',
        'directivo_minimo',
        'directivo_maximo',
        'tecnico_minimo',
        'tecnico_maximo',
    ];
}
