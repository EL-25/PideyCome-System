@forelse($pedidos as $pedido)
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden">
        <div class="absolute top-0 right-0">
            <span class="px-4 py-1 text-[10px] font-black uppercase tracking-tighter 
                {{ $pedido->tipo_orden == 'comer_aqui' ? 'bg-blue-100 text-blue-600' : 'bg-orange-100 text-orange-600' }}">
                {{ str_replace('_', ' ', $pedido->tipo_orden) }}
            </span>
        </div>

        <div class="flex items-center gap-4 mb-6">
            <div class="bg-gray-900 text-white w-12 h-12 rounded-2xl flex items-center justify-center font-black text-lg">
                {{ $pedido->mesa_id ?? 'P' }}
            </div>
            <div>
                <h4 class="font-bold text-gray-900 leading-tight">{{ $pedido->cliente }}</h4>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                    Orden #{{ str_pad($pedido->id, 4, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>

        <div class="space-y-3 mb-8">
            <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Pedido:</p>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ $pedido->bg_color }} text-white uppercase tracking-tighter">
                    {{ $pedido->estado_label }}
                </span>
            </div>
            @foreach($pedido->detalles as $detalle)
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="bg-gray-100 text-gray-700 font-black text-xs px-2 py-0.5 rounded-lg">
                            {{ $detalle->cantidad }}
                        </span>
                        <span class="text-sm font-bold text-gray-700">{{ $detalle->producto_nombre }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        @php
            $configBoton = match($pedido->estado) {
                'ordenada'   => ['texto' => 'Recibir Orden', 'color' => 'bg-blue-600', 'icon' => 'check-circle'],
                'recibida'   => ['texto' => 'Empezar Preparación', 'color' => 'bg-orange-500', 'icon' => 'flame'],
                'preparando' => ['texto' => 'Marcar como Lista', 'color' => 'bg-emerald-600', 'icon' => 'check-check'],
                default      => null,
            };
        @endphp

        @if($configBoton)
            <button type="button" 
                    onclick="avanzarEstado({{ $pedido->id }})"
                    id="btn-avanzar-{{ $pedido->id }}"
                    class="w-full {{ $configBoton['color'] }} text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:opacity-90 transition-all flex items-center justify-center gap-2 shadow-lg shadow-gray-200">
                {{ $configBoton['texto'] }}
            </button>
        @else
            <div class="w-full bg-emerald-50 text-emerald-600 py-3 rounded-2xl font-black text-xs uppercase tracking-widest text-center border border-emerald-100 flex items-center justify-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Lista para entregar
            </div>
        @endif
    </div>
@empty
    <div class="col-span-full flex flex-col items-center justify-center py-20 text-gray-400">
        <i data-lucide="utensils-crossed" class="w-12 h-12 mb-4 opacity-20"></i>
        <p class="font-bold italic">No hay pedidos pendientes en este momento.</p>
    </div>
@endforelse
