<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fruits', function (Blueprint $table) {
            $table->enum('grade', ['A', 'B', 'C', 'D'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('fruits', function (Blueprint $table) {
            $table->enum('grade', ['A', 'B', 'C', 'D'])->nullable(false)->change();
        });
    }
};
