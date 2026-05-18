@extends('admin.layout')

@section('admin_content')
<div class="space-y-8 animate-fade-in">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-2 text-gray-400 mb-2 font-bold text-[10px] uppercase tracking-widest">
                <i data-lucide="dollar-sign" class="w-3 h-3"></i> Ventas Hoy
            </div>
            <p id="stat-ventas" class="text-3xl font-black text-emerald-600">${{ number_format($stats['ventas_hoy'] ?? 0, 2) }}</p>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-2 text-gray-400 mb-2 font-bold text-[10px] uppercase tracking-widest">
                <i data-lucide="trending-up" class="w-3 h-3"></i> Órdenes
            </div>
            <p id="stat-ordenes" class="text-3xl font-black text-gray-900">{{ $stats['ordenes_totales'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-2 text-gray-400 mb-2 font-bold text-[10px] uppercase tracking-widest">
                <i data-lucide="package" class="w-3 h-3"></i> Productos
            </div>
            <p id="stat-productos" class="text-3xl font-black text-gray-900">{{ $stats['productos_activos'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-2 text-gray-400 mb-2 font-bold text-[10px] uppercase tracking-widest">
                <i data-lucide="alert-triangle" class="w-3 h-3"></i> Stock Bajo
            </div>
            <p id="stat-stock-bajo" class="text-3xl font-black text-red-600">{{ $stats['stock_bajo'] }}</p>
        </div>
    </div>

    <!-- Inventory & Products -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/30">
            <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tighter">Gestión de Inventario</h2>
            <button @click="modalProducto = true" class="bg-gray-900 text-white px-6 py-2 rounded-xl font-black text-xs uppercase tracking-widest hover:scale-105 transition">
                + Nuevo Producto
            </button>
        </div>
        <div class="p-8 pb-0">
            <div class="bg-gray-100/50 p-2 rounded-2xl border border-gray-100 inline-flex gap-1 overflow-x-auto max-w-full">
                @foreach(['Todos', 'Comida', 'Bebidas', 'Postres'] as $cat)
                    <button type="button" 
                            @click="categoriaSelected = '{{ $cat }}'"
                            class="px-8 py-2.5 rounded-xl font-black transition whitespace-nowrap text-xs uppercase tracking-wider"
                            :class="categoriaSelected === '{{ $cat }}' ? 'bg-[#E05E1A] text-white shadow-lg shadow-orange-100' : 'bg-white text-gray-400 hover:text-[#E05E1A] shadow-sm'">
                        {{ $cat }}
                    </button>
                @endforeach
            </div>
        </div>
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($productos as $p)
                <div x-show="(categoriaSelected === 'Todos' || '{{ $p->categoria }}' === categoriaSelected) && '{{ strtolower($p->nombre) }}'.includes(search.toLowerCase())" 
                    class="p-6 border border-gray-100 rounded-3xl flex items-center justify-between hover:bg-gray-50 transition group">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <h3 class="font-black text-gray-800 text-lg uppercase tracking-tight">{{ $p->nombre }}</h3>
                            <span class="bg-orange-50 text-[#E05E1A] text-[9px] px-3 py-1 rounded-full font-black border border-orange-100 uppercase tracking-widest">Stock: {{ $p->stock }}</span>
                        </div>
                        <p class="text-xl font-black text-emerald-600 mt-1">${{ number_format($p->precio, 2) }}</p>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <form action="{{ route('admin.actualizarStock', $p->id) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            <input type="number" name="stock" value="{{ $p->stock }}" min="0" 
                                   class="w-16 px-2 py-1 bg-white border border-gray-200 rounded-lg font-bold text-center outline-none focus:border-orange-500 text-sm">
                            <button type="submit" class="p-2 bg-gray-100 rounded-lg hover:bg-orange-500 hover:text-white transition group-hover:bg-white shadow-sm">
                                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            </button>
                        </form>
                        
                        <div class="flex gap-2 border-l border-gray-100 pl-4">
                            <button @click="modalEditProducto = true; editData = {id: '{{ $p->id }}', nombre: '{{ $p->nombre }}', precio: '{{ $p->precio }}', categoria: '{{ $p->categoria }}', stock: '{{ $p->stock }}'}" 
                                    class="p-2 text-gray-400 hover:text-blue-600 transition">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                            <form action="{{ route('admin.productos.destroy', $p->id) }}" method="POST" onsubmit="return confirm('¿Eliminar producto?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- MODAL NUEVO PRODUCTO -->
<div x-show="modalProducto" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-transition x-cloak>
    <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl border-t-[12px] border-[#E05E1A] overflow-hidden" @click.away="modalProducto = false">
        <div class="p-10">
            <h3 class="text-3xl font-black text-gray-800 mb-2 uppercase italic">Nuevo Producto</h3>
            <form action="{{ route('admin.productos.store') }}" method="POST" class="space-y-5 mt-6">
                @csrf
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nombre</label>
                    <input type="text" name="nombre" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-[#E05E1A] outline-none font-bold" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Precio ($)</label>
                        <input type="number" step="0.01" name="precio" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-[#E05E1A] outline-none font-bold" required>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Categoría</label>
                        <select name="categoria" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-[#E05E1A] outline-none font-bold">
                            <option>Comida</option>
                            <option>Bebidas</option>
                            <option>Postres</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Stock Inicial</label>
                    <input type="number" name="stock" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-[#E05E1A] outline-none font-bold" required>
                </div>
                <div class="flex gap-4 pt-6">
                    <button type="button" @click="modalProducto = false" class="flex-1 px-6 py-4 border-2 border-gray-100 rounded-2xl font-black text-gray-400 hover:bg-gray-50 uppercase tracking-widest transition">CANCELAR</button>
                    <button type="submit" class="flex-1 px-6 py-4 bg-[#E05E1A] text-white rounded-2xl font-black hover:scale-105 transition shadow-lg shadow-orange-100 uppercase tracking-widest">GUARDAR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR PRODUCTO -->
<div x-show="modalEditProducto" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-transition x-cloak>
    <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl border-t-[12px] border-blue-500 overflow-hidden" @click.away="modalEditProducto = false">
        <div class="p-10">
            <h3 class="text-3xl font-black text-gray-800 mb-2 uppercase italic">Editar Producto</h3>
            <form :action="'{{ url('/admin/productos/actualizar') }}/' + editData.id" method="POST" class="space-y-5 mt-6">
                @csrf
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nombre</label>
                    <input type="text" name="nombre" x-model="editData.nombre" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 outline-none font-bold" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Precio ($)</label>
                        <input type="number" step="0.01" name="precio" x-model="editData.precio" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 outline-none font-bold" required>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Categoría</label>
                        <select name="categoria" x-model="editData.categoria" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 outline-none font-bold">
                            <option>Comida</option>
                            <option>Bebidas</option>
                            <option>Postres</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Stock Actual</label>
                    <input type="number" name="stock" x-model="editData.stock" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 outline-none font-bold" required>
                </div>
                <div class="flex gap-4 pt-6">
                    <button type="button" @click="modalEditProducto = false" class="flex-1 px-6 py-4 border-2 border-gray-100 rounded-2xl font-black text-gray-400 hover:bg-gray-50 uppercase tracking-widest transition">CANCELAR</button>
                    <button type="submit" class="flex-1 px-6 py-4 bg-blue-500 text-white rounded-2xl font-black hover:scale-105 transition shadow-lg shadow-blue-100 uppercase tracking-widest">ACTUALIZAR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Polling para estadísticas (Dashboard solo)
    function pollStats() {
        axios.get('{{ route('admin.index') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => {
                const stats = response.data.stats;
                if (stats) {
                    const v = document.getElementById('stat-ventas');
                    const o = document.getElementById('stat-ordenes');
                    const p = document.getElementById('stat-productos');
                    const s = document.getElementById('stat-stock-bajo');
                    if(v) v.innerText = '$' + Number(stats.ventas_hoy).toLocaleString('es-MX', { minimumFractionDigits: 2 });
                    if(o) o.innerText = stats.ordenes_totales;
                    if(p) p.innerText = stats.productos_activos;
                    if(s) s.innerText = stats.stock_bajo;
                }
            })
            .catch(err => console.error('Error polling:', err));
    }
    setInterval(pollStats, 10000);
</script>
@endsection