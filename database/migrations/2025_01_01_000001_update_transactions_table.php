<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Torna category_id nulo para permitir "Outros" sem ID
            $table->foreignId('category_id')->nullable()->change();
            // Campo para o nome da categoria temporária
            $table->string('temp_category')->nullable()->after('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable(false)->change();
            $table->dropColumn('temp_category');
        });
    }
};
