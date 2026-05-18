@extends('admin.layout')

@section('admin_content')
<div class="space-y-8 animate-fade-in" x-data="{ openDetail: null }">
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-3xl font-black text-gray-900 tracking-tighter uppercase">Auditoría de Movimientos</h2>
            <p class="text-gray-500 font-medium italic">Historial de acciones del sistema e inventario</p>
        </div>
        
        <form action="{{ route('admin.movimientos') }}" method="GET" class="flex flex-wrap gap-2">
            <input type="date" name="fecha" value="{{ request('fecha') }}" 
                   class="bg-white border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-orange-500 outline-none">
            
            <select name="tipo" class="bg-white border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-orange-500 outline-none">
                <option value="todos">Todos los tipos</option>
                <option value="producto" {{ request('tipo') == 'producto' ? 'selected' : '' }}>Productos</option>
                <option value="inventario" {{ request('tipo') == 'inventario' ? 'selected' : '' }}>Inventario</option>
                <option value="usuario" {{ request('tipo') == 'usuario' ? 'selected' : '' }}>Usuarios</option>
            </select>
            
            <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-black transition-all shadow-lg shadow-gray-200">
                Filtrar
            </button>
        </form>
    </header>

    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[800px]">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Fecha y Hora</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Usuario</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tipo</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Acción</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Detalles</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($movimientos as $mov)
                    <tr x-show="'{{ strtolower($mov->user->name ?? 'sistema') }} {{ strtolower($mov->tipo) }} {{ strtolower($mov->accion) }} {{ strtolower($mov->descripcion) }}'.includes(search.toLowerCase())"
                        class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <p class="text-sm font-black text-gray-900">{{ $mov->created_at->format('d/m/Y') }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $mov->created_at->format('H:i:s') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-black text-xs uppercase">
                                    {{ substr($mov->user->name ?? 'S', 0, 1) }}
                                </div>
                                <span class="text-sm font-bold text-gray-700">{{ $mov->user->name ?? 'Sistema' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter border 
                                {{ $mov->tipo == 'producto' ? 'bg-blue-50 text-blue-600 border-blue-100' : 
                                   ($mov->tipo == 'inventario' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-purple-50 text-purple-600 border-purple-100') }}">
                                {{ $mov->tipo }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-600 capitalize">
                            {{ $mov->accion }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button @click="openDetail === {{ $mov->id }} ? openDetail = null : openDetail = {{ $mov->id }}" 
                                    class="p-2 hover:bg-white rounded-xl transition-all border border-transparent hover:border-gray-100 text-gray-400 hover:text-gray-900">
                                <i data-lucide="chevron-down" class="w-5 h-5 transition-transform" :class="openDetail === {{ $mov->id }} ? 'rotate-180' : ''"></i>
                            </button>
                        </td>
                    </tr>
                    <tr x-show="openDetail === {{ $mov->id }} && '{{ strtolower($mov->user->name ?? 'sistema') }} {{ strtolower($mov->tipo) }} {{ strtolower($mov->accion) }} {{ strtolower($mov->descripcion) }}'.includes(search.toLowerCase())"
                        x-transition.opacity class="bg-gray-50/30">
                        <td colspan="5" class="px-8 py-6">
                            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-inner">
                                <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Descripción Completa</p>
                                <p class="text-gray-800 font-medium leading-relaxed">{{ $mov->descripcion }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $movimientos->appends(request()->all())->links() }}
    </div>
</div>
@endsection
