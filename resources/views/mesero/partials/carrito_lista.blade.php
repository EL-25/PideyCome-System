@forelse($carrito as $id => $item)
    <div class="flex flex-col bg-gray-50 p-4 rounded-2xl gap-3 group border border-gray-100 animate-fade-in mb-3">
        <div class="flex justify-between items-start">
            <div>
                <p class="font-bold text-gray-900 text-sm leading-tight">{{ $item['nombre'] }}</p>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ $item['categoria'] }}</p>
            </div>
            <button type="button" onclick="eliminarDelCarritoAjax('{{ $id }}')" class="text-gray-300 hover:text-red-500 transition">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2 bg-white border border-gray-100 rounded-full p-1 shadow-inner">
                {{-- Botón Menos --}}
                <button type="button" 
                        onclick="actualizarCantidadAjax('{{ $id }}', 'decrementar')" 
                        class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-orange-500 transition">
                    <i data-lucide="minus" class="w-4 h-4"></i>
                </button>
                
                <span class="font-black text-gray-900 text-sm w-5 text-center">{{ $item['cantidad'] }}</span>
                
                {{-- Botón Más --}}
                <button type="button" 
                        onclick="actualizarCantidadAjax('{{ $id }}', 'incrementar')" 
                        class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-orange-500 transition"
                        {{ ($item['cantidad'] >= ($item['stock_max'] ?? 99)) ? 'disabled' : '' }}>
                    <i data-lucide="plus" class="w-4 h-4"></i>
                </button>
            </div>
            
            <p class="font-black text-gray-950 text-base">
                ${{ number_format($item['precio'] * $item['cantidad'], 2) }}
            </p>
        </div>
    </div>
@empty
    <div class="py-10 flex flex-col items-center justify-center text-gray-300 text-center">
        <i data-lucide="shopping-cart" class="w-12 h-12 mb-4 opacity-10"></i>
        <p class="font-bold text-gray-400">Aún no hay productos</p>
    </div>
@endforelse
