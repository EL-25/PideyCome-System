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
    Schema::create('pedido_detalles', function (Blueprint $table) {
        $table->id();
        
        // Relación con la cabecera (pedidos)
        // onDelete('cascade') significa que si borras el pedido, se borran sus platillos
        $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
        
        $table->string('producto_nombre'); // Guardamos el nombre por si el producto cambia de precio a futuro
        $table->integer('cantidad');
        $table->decimal('precio', 10, 2); // Precio unitario al momento de la venta
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_detalles');
    }
};
