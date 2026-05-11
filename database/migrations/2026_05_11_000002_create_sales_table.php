<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id('idsales');
            $table->unsignedBigInteger('idtoko');
            $table->double('latitude');
            $table->double('longitude');
            $table->double('accuracy');
            $table->integer('jarak');
            $table->char('status', 20);
            $table->timestamp('waktu');

            $table->foreign('idtoko')->references('idtoko')->on('toko');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
