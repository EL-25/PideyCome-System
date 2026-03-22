@foreach($ordenes as $orden)
    <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all relative overflow-hidden">
        
        <div class="flex justify-between items-start mb-4">
            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-tighter border
                {{ $orden->estado == 'Ordenada' ? 'bg-blue-50 text-blue-600 border-blue-100' : '' }}
                {{ $orden->estado == 'Preparando' ? 'bg-yellow-50 text-yellow-600 border-yellow-100' : '' }}
                {{ $orden->estado == 'Despachada' ? 'bg-green-50 text-green-600 border-green-100' : '' }}
            ">
                {{ $orden->estado }}
            </span>
            <span class="text-[10px] font-bold text-gray-400">#{{ $orden->id }}</span>
        </div>

        <h3 class="text-xl font-black text-gray-900 mb-1">{{ $orden->cliente }}</h3>
        <p class="text-sm font-bold text-orange-500 mb-4">
            {{ $orden->tipo_orden == 'comer_aqui' ? 'Mesa ' . $orden->mesa_id : 'Para Llevar' }}
        </p>

        <div class="space-y-2 border-t border-gray-50 pt-4 mb-4">
            @foreach($orden->detalles as $detalle)
                <div class="flex justify-between text-xs font-medium">
                    <span class="text-gray-500"><b class="text-gray-900">{{ $detalle->cantidad }}x</b> {{ $detalle->producto_nombre }}</span>
                    <span class="text-gray-400">${{ number_format($detalle->precio * $detalle->cantidad, 2) }}</span>
                </div>
            @endforeach
        </div>

        <div class="flex justify-between items-center pt-2">
            <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $orden->created_at->diffForHumans() }}</span>
            <span class="text-lg font-black text-gray-900">${{ number_format($orden->total, 2) }}</span>
        </div>

        @if($orden->estado == 'Despachada')
            <button class="w-full mt-4 bg-green-600 text-white font-black py-3 rounded-xl flex items-center justify-center gap-2 hover:bg-green-700 transition-all shadow-lg shadow-green-100">
                <i data-lucide="check-circle-2" class="w-4 h-4"></i> Entregar a Mesa
            </button>
        @endif
    </div>
@endforeach

@if($ordenes->isEmpty())
    <div class="col-span-full py-20 text-center bg-white rounded-[2rem] border-2 border-dashed border-gray-100">
        <i data-lucide="clipboard-list" class="w-12 h-12 text-gray-200 mx-auto mb-4"></i>
        <p class="text-gray-400 font-bold">No hay órdenes activas para mostrar.</p>
    </div>
@endif
