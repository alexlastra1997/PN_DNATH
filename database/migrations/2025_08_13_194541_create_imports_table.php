<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    Schema::create('imports', function (Blueprint $t) {
        $t->id();
        $t->string('filename');
        $t->string('status')->default('queued'); // queued|processing|done|failed
        $t->timestamp('started_at')->nullable();
        $t->timestamp('finished_at')->nullable();
        $t->timestamps();
    });
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('imports');
    }
};
