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
        Schema::table('characters', function (Blueprint $table) {
            $table->string('body_file')->nullable()->after('xp')->comment('Relative path to body asset');
            $table->string('eyes_file')->nullable()->after('body_file')->comment('Relative path to eyes asset');
            $table->string('hair_file')->nullable()->after('eyes_file')->comment('Relative path to hair asset');
            $table->string('top_file')->nullable()->after('hair_file')->comment('Relative path to top asset');
            $table->string('bottom_file')->nullable()->after('top_file')->comment('Relative path to bottom asset');
            $table->string('acc_file')->nullable()->after('bottom_file')->comment('Relative path to accessory asset');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn([
                'body_file',
                'eyes_file',
                'hair_file',
                'top_file',
                'bottom_file',
                'acc_file',
            ]);
        });
    }
};
