@forelse($carrito as $id => $item)
    <div class="flex flex-col bg-gray-50 p-4 rounded-2xl gap-3 group border border-gray-100 animate-fade-in mb-3">
        <div class="flex justify-between items-start">
            <div>
                <p class="font-bold text-gray-900 text-sm leading-tight">{{ $item['nombre'] }}</p>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ $item['categoria'] }}</p>
            </div>
            {{-- Botón Eliminar --}}
            <button type="button" onclick="eliminarDelCarritoAjax('{{ $id }}')" class="text-gray-300 hover:text-red-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>

        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2 bg-white border border-gray-100 rounded-full p-1 shadow-inner">
                {{-- Botón Menos --}}
                <button type="button" 
                        onclick="actualizarCantidadAjax('{{ $id }}', 'decrementar')" 
                        class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-orange-500 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                </button>
                
                <span class="font-black text-gray-900 text-sm w-5 text-center">{{ $item['cantidad'] }}</span>
                
                {{-- Botón Más --}}
                <button type="button" 
                        onclick="actualizarCantidadAjax('{{ $id }}', 'incrementar')" 
                        class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-orange-500 transition disabled:opacity-30 disabled:cursor-not-allowed"
                        {{ ($item['cantidad'] >= ($item['stock_max'] ?? 99)) ? 'disabled' : '' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
            </div>
            
            <p class="font-black text-gray-950 text-base">
                ${{ number_format($item['precio'] * $item['cantidad'], 2) }}
            </p>
        </div>
    </div>
@empty
    <div class="py-10 flex flex-col items-center justify-center text-gray-300 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mb-4 opacity-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <p class="font-bold text-gray-400 uppercase tracking-widest text-[10px]">La orden está vacía</p>
    </div>
@endforelse
