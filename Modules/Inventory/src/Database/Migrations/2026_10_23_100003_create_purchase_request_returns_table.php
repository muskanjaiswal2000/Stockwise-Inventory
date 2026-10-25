<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_request_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('processed_by')->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->string('condition');
            $table->string('status')->default('accepted');
            $table->text('notes')->nullable();
            $table->timestamp('processed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_request_returns');
    }
};
