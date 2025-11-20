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
            $table->string('skin_hex', 7)->default('#ffffff')->after('avatar');
            $table->string('boxer_hex', 7)->default('#6c757d')->after('skin_hex');
            $table->enum('hair_style', ['none', 'buzz', 'short'])->default('none')->after('boxer_hex');
            $table->string('hair_hex', 7)->default('#000000')->after('hair_style');
            $table->string('eyes_hex', 7)->default('#2b2b2b')->after('hair_hex');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn(['skin_hex', 'boxer_hex', 'hair_style', 'hair_hex', 'eyes_hex']);
        });
    }
};
