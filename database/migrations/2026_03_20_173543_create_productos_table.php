<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('productos', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->decimal('precio', 8, 2);
        $table->string('categoria'); // Comida, Bebidas, Postres
        $table->integer('stock');
        $table->string('imagen')->nullable(); // Para el logo/foto del plato
        $table->timestamps();
    });
}
};
