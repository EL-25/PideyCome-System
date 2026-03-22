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
            
            // Relación con el Mesero (Usuario que crea la orden)
            // Se usa constrained para mantener la integridad referencial
            $table->foreignId('user_id')->constrained('users'); 

            // Nombre del cliente: Requisito obligatorio para todas las vistas
            $table->string('cliente');

            // Tipo de orden: 'comer_aqui' o 'para_llevar'
            $table->string('tipo_orden'); 
            
            // ID de Mesa: Cambiado a string para soportar identificadores de "Para Llevar"
            // Es nullable porque el prompt indica que 'para_llevar' tiene un ID diferente
            $table->string('mesa_id')->nullable(); 

            // Flujo de Estados: Requisito técnico del sistema
            // Ordenada (Amarillo) -> Recibida (Azul) -> Preparando (Naranja) -> Despachada (Verde)
            $table->enum('estado', ['ordenada', 'recibida', 'preparando', 'despachada'])
                  ->default('ordenada'); 

            // Total de la orden con precisión para moneda
            $table->decimal('total', 10, 2)->default(0); 

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
