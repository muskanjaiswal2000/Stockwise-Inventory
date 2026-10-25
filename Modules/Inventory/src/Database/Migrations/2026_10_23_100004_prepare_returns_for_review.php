<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_request_returns', function (Blueprint $table) {
            $table->dropForeign(['processed_by']);
            $table->unsignedBigInteger('processed_by')->nullable()->change();
            $table->string('condition')->default('pending')->change();
            $table->string('status')->default('pending')->change();
            $table->timestamp('processed_at')->nullable()->change();
            $table->foreign('processed_by')->references('id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_request_returns', function (Blueprint $table) {
            $table->dropForeign(['processed_by']);
            $table->unsignedBigInteger('processed_by')->nullable(false)->change();
            $table->string('condition')->default('usable')->change();
            $table->string('status')->default('accepted')->change();
            $table->timestamp('processed_at')->nullable(false)->change();
            $table->foreign('processed_by')->references('id')->on('users')->restrictOnDelete();
        });
    }
};
