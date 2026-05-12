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
        Schema::table('pedidos', function (Blueprint $table) {
            // Agregamos las columnas faltantes que no estaban en migraciones anteriores
            $table->string('metodo_pago')->nullable();
            $table->foreignId('cajera_id')->nullable()->constrained('users');
            
            // Detalles de facturación
            $table->string('tipo_comprobante')->default('ticket');
            $table->string('cliente_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['cajera_id']);
            $table->dropColumn(['tipo_comprobante', 'cliente_email', 'metodo_pago', 'cajera_id']);
        });
    }
};
