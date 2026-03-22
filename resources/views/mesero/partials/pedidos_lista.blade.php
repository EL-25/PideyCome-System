@forelse($ordenes as $orden)
    <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all relative overflow-hidden animate-fade-in mb-4">
        
        {{-- Barra lateral de color dinámica usando tu Accessor bg_color --}}
        <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $orden->bg_color }}"></div>

        <div class="flex justify-between items-start mb-4">
            {{-- Usamos el accessor estado_label y bg_color para el Badge --}}
            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-tighter border text-white {{ $orden->bg_color }} shadow-sm">
                {{ $orden->estado_label }}
            </span>
            <span class="text-[10px] font-black text-gray-300">ID #{{ $orden->id }}</span>
        </div>

        <h3 class="text-xl font-black text-gray-900 mb-1 leading-tight tracking-tighter uppercase">{{ $orden->cliente }}</h3>
        
        <div class="flex items-center gap-2 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            </svg>
            <p class="text-xs font-black text-orange-500 uppercase tracking-widest">
                {{ $orden->tipo_orden == 'comer_aqui' ? 'Mesa ' . ($orden->mesa_id ?? 'S/N') : 'Para Llevar' }}
            </p>
        </div>

        {{-- Detalles del Pedido --}}
        <div class="space-y-2 border-t border-dashed border-gray-100 pt-4 mb-4">
            @foreach($orden->detalles as $detalle)
                <div class="flex justify-between text-xs items-center bg-gray-50/50 p-2 rounded-lg">
                    <span class="text-gray-600 font-medium">
                        <b class="text-gray-950 font-black mr-1">{{ $detalle->cantidad }}x</b> 
                        {{ $detalle->producto_nombre }}
                    </span>
                    <span class="text-gray-400 font-bold">${{ number_format($detalle->precio * $detalle->cantidad, 2) }}</span>
                </div>
            @endforeach
        </div>

        <div class="flex justify-between items-center pt-2">
            <div class="flex flex-col">
                <span class="text-[9px] text-gray-400 font-black uppercase leading-none mb-1">Total de Orden</span>
                <span class="text-2xl font-black text-gray-950 leading-none">${{ number_format($orden->total, 2) }}</span>
            </div>
            <span class="text-[10px] text-gray-400 font-bold uppercase bg-gray-50 px-2 py-1 rounded-md">
                Hace {{ $orden->created_at->diffForHumans(null, true) }}
            </span>
        </div>

        {{-- Botón de Acción Final: Solo aparece cuando cocina termina --}}
        @if($orden->estado == 'despachada')
            <button class="w-full mt-5 bg-green-600 text-white font-black py-4 rounded-2xl flex items-center justify-center gap-2 hover:bg-green-700 transition-all shadow-lg shadow-green-100 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg> 
                ENTREGAR A MESA
            </button>
        @endif
    </div>
@empty
    <div class="col-span-full py-24 text-center bg-white rounded-[3rem] border-2 border-dashed border-gray-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-100 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        <p class="text-gray-400 font-black uppercase tracking-tighter text-xl">Sin órdenes en esta sección</p>
    </div>
@endforelse
