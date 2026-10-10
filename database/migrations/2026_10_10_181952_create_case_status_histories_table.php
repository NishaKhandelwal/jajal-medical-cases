
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_status_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('medical_case_id')
                ->constrained('cases')
                ->cascadeOnDelete();

            $table->string('old_status', 20)->nullable();
            $table->string('new_status', 20);

            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['medical_case_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_status_histories');
    }
};
