<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('queues', function (Blueprint $table) {

            $table->id();
            $table->foreignId('poli_id')->constrained('poli')->onDelete('cascade');
            $table->string('queue_number');
            $table->string('customer_name');
            $table->integer('queue_order');
            $table->enum('status', ['waiting','called', 'late', 'done'])->default('waiting');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('queue_order');
            $table->index('poli_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue');
    }
};
