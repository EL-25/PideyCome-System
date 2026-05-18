@extends('admin.layout')

@section('admin_content')
<div class="space-y-8 animate-fade-in" x-data="{ openSale: null }">
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-3xl font-black text-gray-900 tracking-tighter uppercase">Historial de Ventas</h2>
            <p class="text-emerald-600 font-bold uppercase tracking-widest text-[10px]">Total del periodo: ${{ number_format($totalVentas, 2) }}</p>
        </div>
        
        <form action="{{ route('admin.ventas') }}" method="GET" class="flex flex-wrap gap-2">
            <input type="date" name="fecha" value="{{ request('fecha') }}" 
                   class="bg-white border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-emerald-500 outline-none">
            
            <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-black transition-all shadow-lg shadow-gray-200">
                Filtrar Ventas
            </button>
        </form>
    </header>

    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[800px]">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">ID</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Fecha Pago</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Cliente / Mesa</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Mesero</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Total</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Ver Orden</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($ventas as $venta)
                    <tr x-show="'#{{ $venta->id }} {{ strtolower($venta->cliente) }} {{ strtolower($venta->mesero->name ?? 'sistema') }} mesa {{ $venta->mesa_id ?? '' }} {{ strtolower($venta->metodo_pago) }} {{ strtolower($venta->tipo_comprobante) }}'.includes(search.toLowerCase())"
                        class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4 text-sm font-black text-gray-400">#{{ $venta->id }}</td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-black text-gray-900">{{ $venta->updated_at->format('d/m/Y') }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $venta->updated_at->format('H:i') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-black text-gray-900 uppercase">{{ $venta->cliente }}</p>
                            <p class="text-[10px] text-emerald-600 font-bold uppercase">{{ $venta->tipo_orden == 'comer_aqui' ? 'Mesa ' . $venta->mesa_id : 'Para Llevar' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-600">
                            {{ $venta->mesero->name ?? 'Sistema' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-lg font-black text-gray-950">${{ number_format($venta->total, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button @click="openSale === {{ $venta->id }} ? openSale = null : openSale = {{ $venta->id }}" 
                                    class="p-2 hover:bg-emerald-50 rounded-xl transition-all border border-transparent hover:border-emerald-100 text-gray-400 hover:text-emerald-600">
                                <i data-lucide="eye" class="w-5 h-5"></i>
                            </button>
                        </td>
                    </tr>
                    <tr x-show="openSale === {{ $venta->id }} && '#{{ $venta->id }} {{ strtolower($venta->cliente) }} {{ strtolower($venta->mesero->name ?? 'sistema') }} mesa {{ $venta->mesa_id ?? '' }} {{ strtolower($venta->metodo_pago) }} {{ strtolower($venta->tipo_comprobante) }}'.includes(search.toLowerCase())"
                        x-transition.scale.origin.top class="bg-gray-50/50">
                        <td colspan="6" class="px-8 py-6">
                            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-inner max-w-2xl">
                                <div class="flex justify-between items-center mb-4 border-b border-dashed border-gray-100 pb-4">
                                    <h4 class="font-black text-gray-900 uppercase tracking-tighter text-sm">Detalle de Productos</h4>
                                    <span class="text-[10px] bg-gray-900 text-white px-3 py-1 rounded-full font-black uppercase">{{ $venta->metodo_pago }}</span>
                                </div>
                                <div class="space-y-3">
                                    @foreach($venta->detalles as $detalle)
                                    <div class="flex justify-between items-center bg-gray-50 p-3 rounded-2xl">
                                        <div class="flex items-center gap-3">
                                            <span class="w-8 h-8 bg-white border border-gray-100 rounded-lg flex items-center justify-center font-black text-xs">{{ $detalle->cantidad }}x</span>
                                            <span class="text-xs font-bold text-gray-700 uppercase">{{ $detalle->producto_nombre }}</span>
                                        </div>
                                        <span class="text-xs font-black text-gray-400">${{ number_format($detalle->precio * $detalle->cantidad, 2) }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mt-4 pt-4 border-t border-dashed border-gray-100 flex justify-between items-center">
                                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Tipo: {{ $venta->tipo_comprobante }}</p>
                                    <p class="text-lg font-black text-gray-900">Total: ${{ number_format($venta->total, 2) }}</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $ventas->appends(request()->all())->links() }}
    </div>
</div>
@endsection
