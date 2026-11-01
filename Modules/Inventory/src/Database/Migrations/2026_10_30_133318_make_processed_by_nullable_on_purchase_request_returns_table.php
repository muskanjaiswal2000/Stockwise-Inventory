
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
            
            $table->foreignId('processed_by')
                ->nullable()
                ->change();

            $table->foreign('processed_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_request_returns', function (Blueprint $table) {
            $table->dropForeign(['processed_by']);

            $table->foreignId('processed_by')
                ->nullable(false)
                ->change();

            $table->foreign('processed_by')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();
        });
    }
};
