<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsuariosImport; // tu import con WithChunkReading

class ImportarExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800; // 30 min
    public $tries   = 1;

    public function __construct(public string $path, public int $importId) {}

    public function handle(): void
    {
        // marca en BD: en_proceso
        \DB::table('imports')->where('id', $this->importId)->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        Excel::import(new UsuariosImport, storage_path('app/'.$this->path));

        \DB::table('imports')->where('id', $this->importId)->update([
            'status' => 'done',
            'finished_at' => now(),
        ]);
    }
}

