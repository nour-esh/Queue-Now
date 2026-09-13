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
    Schema::create('tickets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('service_id')->constrained()->restrictOnDelete();

        $table->unsignedInteger('ticket_number');
        $table->date('queue_date');

        $table->enum('status', [
            'WAITING', 'CALLED', 'SERVING', 'DONE', 'SKIPPED', 'CANCELLED'
        ])->default('WAITING');

        $table->timestamp('called_at')->nullable();
        $table->timestamp('started_at')->nullable();
        $table->timestamp('finished_at')->nullable();
        $table->timestamp('skipped_at')->nullable();
        $table->timestamp('cancelled_at')->nullable();

        $table->timestamps(); // created_at + updated_at

        $table->unique(['service_id', 'queue_date', 'ticket_number']);
        $table->index(['service_id', 'status']);
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
