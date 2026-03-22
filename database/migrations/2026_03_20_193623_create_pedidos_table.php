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
    Schema::create('pedidos', function (Blueprint $table) {
        $table->id();
        
        // El Mesero: Relacionamos con la tabla 'users'
        // Si borras un usuario, no queremos borrar sus ventas (usamos constrained)
        $table->foreignId('user_id')->constrained('users'); 

        $table->string('cliente');
        $table->string('tipo_orden'); // Guardará: 'comer_aqui' o 'para_llevar'
        
        // Mesa: Puede ser nula si la orden es para llevar
        $table->integer('mesa_id')->nullable(); 

        // Estado: Por defecto es 'Ordenada' (esto lo usaremos en Cocina)
        $table->string('estado')->default('Ordenada'); 

        // Total: 10 dígitos en total, 2 decimales (ej: 99,999,999.99)
        $table->decimal('total', 10, 2); 

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
