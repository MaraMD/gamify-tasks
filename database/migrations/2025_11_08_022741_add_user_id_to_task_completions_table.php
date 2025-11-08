<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('task_completions', function (Blueprint $table) {
            // si ya existiera por alguna razón, evita duplicados
            if (!Schema::hasColumn('task_completions', 'user_id')) {
                $table->foreignId('user_id')
                      ->after('task_id')
                      ->constrained() // users.id
                      ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('task_completions', function (Blueprint $table) {
            if (Schema::hasColumn('task_completions', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
