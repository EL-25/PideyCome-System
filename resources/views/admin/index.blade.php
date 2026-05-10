<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PideYCome - Administración</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #F28705; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#F2E7DC] min-h-screen font-sans" x-data="{ 
        tab: 'productos', 
        search: '', 
        fecha: '{{ date('Y-m-d') }}',
        modalProducto: false,
        modalEditProducto: false,
        modalUsuario: false,
        modalEditUsuario: false,
        editData: { id: '', nombre: '', precio: '', categoria: '', stock: '' },
        userEditData: { id: '', name: '', username: '', role: '' }
    }">

    <nav class="bg-white shadow-sm border-b border-gray-200 px-6 py-3 flex justify-between items-center sticky top-0 z-40">
        <div class="flex items-center gap-2">
            <div class="bg-[#F28705] p-2 rounded-lg">
                <i data-lucide="utensils" class="w-5 h-5 text-white"></i>
            </div>
            <div>
                <p class="font-bold text-gray-800 leading-none">Sistema Restaurante</p>
                <p class="text-[10px] text-gray-500 uppercase font-black tracking-widest">{{ auth()->user()->name }} - {{ auth()->user()->role }}</p>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="flex items-center gap-2 px-4 py-1.5 border-2 border-gray-100 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 transition">
                <i data-lucide="log-out" class="w-4 h-4"></i> Cerrar Sesión
            </button>
        </form>
    </nav>

    <div class="max-w-7xl mx-auto p-6">
        
        <div class="flex items-center gap-4 mb-8">
            <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100">
                <i data-lucide="layout-dashboard" class="w-8 h-8 text-[#F28705]"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-gray-800">Administración</h1>
                <p class="text-gray-500 font-medium">Panel de control integral</p>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 px-6 py-4 rounded-2xl mb-8 font-bold flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-50 border border-red-100 text-red-600 px-6 py-4 rounded-2xl mb-8 font-bold flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            <div>
                <p>Ocurrió un error:</p>
                <ul class="text-xs font-medium list-disc ml-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center gap-2 text-gray-400 mb-2 font-bold text-[10px] uppercase tracking-widest">
                    <i data-lucide="dollar-sign" class="w-3 h-3"></i> Ventas Hoy
                </div>
                <p class="text-3xl font-black text-green-600">${{ number_format($stats['ventas_hoy'] ?? 0, 2) }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center gap-2 text-gray-400 mb-2 font-bold text-[10px] uppercase tracking-widest">
                    <i data-lucide="trending-up" class="w-3 h-3"></i> Órdenes Totales
                </div>
                <p class="text-3xl font-black text-gray-800">{{ $stats['ordenes_totales'] }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center gap-2 text-gray-400 mb-2 font-bold text-[10px] uppercase tracking-widest">
                    <i data-lucide="package" class="w-3 h-3"></i> Productos Activos
                </div>
                <p class="text-3xl font-black text-gray-800">{{ $stats['productos_activos'] }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center gap-2 text-gray-400 mb-2 font-bold text-[10px] uppercase tracking-widest">
                    <i data-lucide="alert-triangle" class="w-3 h-3"></i> Stock Bajo
                </div>
                <p class="text-3xl font-black text-red-600">{{ $stats['stock_bajo'] }}</p>
            </div>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            <div class="flex bg-white p-1 rounded-2xl border border-gray-200 shadow-sm w-full md:w-auto">
                <template x-for="(label, key) in {productos: 'Productos', inventario: 'Inventario', movimientos: 'Movimientos', usuarios: 'Usuarios'}">
                    <button @click="tab = key; search = ''" 
                        :class="tab === key ? 'bg-[#F28705] text-white' : 'text-gray-500 hover:bg-gray-50'"
                        class="flex-1 md:flex-none px-6 py-2 rounded-xl text-sm font-black transition duration-200 uppercase tracking-tighter"
                        x-text="label">
                    </button>
                </template>
            </div>

            <div class="relative w-full md:w-80">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input type="text" x-model="search" :placeholder="'Buscar en ' + tab + '...'"
                    class="w-full pl-12 pr-4 py-3 bg-white border-2 border-transparent rounded-2xl focus:border-[#F28705] outline-none shadow-sm font-bold text-sm transition">
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden min-h-[500px]">
            
            <div x-show="tab === 'productos'" class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl font-black text-gray-800 uppercase italic">Gestión de Menú</h2>
                    <button @click="modalProducto = true" class="bg-[#F28705] text-white px-6 py-3 rounded-2xl font-black flex items-center gap-2 hover:scale-105 transition shadow-lg shadow-orange-100">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i> NUEVO PRODUCTO
                    </button>
                </div>
                <div class="grid grid-cols-1 gap-3">
                    @foreach($productos as $p)
                    <div x-show="'{{ strtolower($p->nombre) }}'.includes(search.toLowerCase())" 
                        class="flex items-center justify-between p-5 border border-gray-100 rounded-3xl hover:bg-gray-50 transition group">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="font-black text-gray-800 text-xl">{{ $p->nombre }}</h3>
                                <span class="bg-gray-100 text-gray-500 text-[10px] px-3 py-1 rounded-full font-black uppercase tracking-widest border border-gray-200">{{ $p->categoria }}</span>
                                @if($p->stock <= 0)
                                    <span class="bg-red-500 text-white text-[10px] px-3 py-1 rounded-full font-black uppercase tracking-widest shadow-sm">AGOTADO</span>
                                @else
                                    <span class="bg-orange-50 text-[#F28705] text-[10px] px-3 py-1 rounded-full font-black border border-orange-100 uppercase tracking-widest">Stock: {{ $p->stock }}</span>
                                @endif
                            </div>
                            <p class="text-2xl font-black text-green-600">${{ number_format($p->precio, 2) }}</p>
                        </div>
                        <div class="flex gap-3">
                            <button @click="modalEditProducto = true; editData = {id: '{{ $p->id }}', nombre: '{{ $p->nombre }}', precio: '{{ $p->precio }}', categoria: '{{ $p->categoria }}', stock: '{{ $p->stock }}'}" 
                                class="p-3 border-2 border-gray-100 rounded-2xl text-gray-400 hover:border-[#F28705] hover:text-[#F28705] transition">
                                <i data-lucide="edit-3" class="w-5 h-5"></i>
                            </button>
                            <form action="{{ route('admin.productos.destroy', $p->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este producto?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-3 border-2 border-gray-100 rounded-2xl text-gray-400 hover:bg-red-50 hover:border-red-100 hover:text-red-500 transition"><i data-lucide="trash-2" class="w-5 h-5"></i></button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div x-show="tab === 'inventario'" class="p-8" x-cloak>
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl font-black text-gray-800 uppercase italic">Control de Insumos / Stock</h2>
                </div>
                <div class="space-y-4">
                    @foreach($productos as $p)
                    <div x-show="'{{ strtolower($p->nombre) }}'.includes(search.toLowerCase())" 
                        class="p-6 border border-gray-100 rounded-3xl flex items-center justify-between">
                        <div>
                            <p class="font-black text-gray-800 text-lg">{{ $p->nombre }}</p>
                            <p class="text-sm text-gray-400 font-bold uppercase tracking-tight">Stock Actual: 
                                <span class="{{ $p->stock < 5 ? 'text-red-500' : 'text-gray-700' }}">{{ $p->stock }}</span>
                            </p>
                        </div>
                        <form action="{{ route('admin.actualizarStock', $p->id) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            <input type="number" name="stock" value="{{ $p->stock }}" class="w-20 px-3 py-2 bg-gray-50 border-2 border-gray-100 rounded-xl font-bold outline-none focus:border-[#F28705]">
                            <button type="submit" class="bg-white border-2 border-gray-100 px-4 py-2 rounded-xl font-black text-xs text-gray-500 hover:border-[#F28705] hover:text-[#F28705] transition text-center uppercase">ACTUALIZAR</button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>

            <div x-show="tab === 'movimientos'" class="p-8" x-cloak>
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                    <div>
                        <h2 class="text-2xl font-black text-gray-800 uppercase italic">Historial del Sistema</h2>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <input type="date" x-model="fecha" class="px-4 py-2 border-2 border-gray-100 rounded-xl font-bold text-gray-600 outline-none focus:border-[#F28705]">
                    </div>
                </div>
                <div class="py-24 text-center border-4 border-dashed border-gray-50 rounded-[2rem]">
                    <i data-lucide="history" class="w-16 h-16 text-gray-100 mx-auto mb-4"></i>
                    <p class="text-gray-300 font-black italic text-xl uppercase tracking-widest">No hay movimientos registrados para esta fecha</p>
                </div>
            </div>

            <div x-show="tab === 'usuarios'" class="p-8" x-cloak>
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl font-black text-gray-800 uppercase italic">Gestión de Personal</h2>
                    <button @click="modalUsuario = true" class="bg-[#F28705] text-white px-6 py-3 rounded-2xl font-black shadow-lg shadow-orange-100">+ NUEVO USUARIO</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($usuarios as $u)
                    <div x-show="'{{ strtolower($u->name) }}'.includes(search.toLowerCase())" 
                        class="p-5 border border-gray-100 rounded-3xl flex items-center justify-between hover:border-[#F28705] transition group">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-[#F2E7DC] rounded-2xl flex items-center justify-center text-[#F28705] font-black text-2xl shadow-inner">{{ substr($u->name, 0, 1) }}</div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="font-black text-gray-800 text-lg">{{ $u->name }}</p>
                                    <span class="bg-gray-100 text-gray-500 text-[9px] px-2 py-0.5 rounded-md font-black uppercase tracking-widest">{{ $u->role }}</span>
                                </div>
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Usuario: {{ $u->username }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($u->temp_passwd)
                            <span class="text-[9px] font-black text-orange-500 bg-orange-50 px-2 py-1 rounded-full uppercase tracking-tighter italic mr-2">Pswd Temporal</span>
                            @endif
                            <button @click="modalEditUsuario = true; userEditData = {id: '{{ $u->id }}', name: '{{ $u->name }}', username: '{{ $u->username }}', role: '{{ $u->role }}'}" 
                                class="p-2 border border-gray-100 rounded-xl text-gray-400 hover:text-blue-500 hover:border-blue-200 transition">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </button>
                            <form action="{{ route('admin.usuarios.destroy', $u->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar a este usuario?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 border border-gray-100 rounded-xl text-gray-400 hover:text-red-500 hover:border-red-200 transition">
                                    <i data-lucide="user-minus" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>


    <!-- MODAL NUEVO PRODUCTO -->
    <div x-show="modalProducto" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-transition x-cloak>
        <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl border-t-[12px] border-[#F28705] overflow-hidden" @click.away="modalProducto = false">
            <div class="p-10">
                <h3 class="text-3xl font-black text-gray-800 mb-2 uppercase italic">Nuevo Producto</h3>
                <form action="{{ route('admin.productos.store') }}" method="POST" class="space-y-5 mt-6">
                    @csrf
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nombre</label>
                        <input type="text" name="nombre" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-[#F28705] outline-none font-bold" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Precio ($)</label>
                            <input type="number" step="0.01" name="precio" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-[#F28705] outline-none font-bold" required>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Categoría</label>
                            <select name="categoria" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-[#F28705] outline-none font-bold">
                                <option>Comida</option>
                                <option>Bebidas</option>
                                <option>Postres</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Stock Inicial</label>
                        <input type="number" name="stock" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-[#F28705] outline-none font-bold" required>
                    </div>
                    <div class="flex gap-4 pt-6">
                        <button type="button" @click="modalProducto = false" class="flex-1 px-6 py-4 border-2 border-gray-100 rounded-2xl font-black text-gray-400 hover:bg-gray-50 uppercase tracking-widest transition">CANCELAR</button>
                        <button type="submit" class="flex-1 px-6 py-4 bg-[#F28705] text-white rounded-2xl font-black hover:scale-105 transition shadow-lg shadow-orange-100 uppercase tracking-widest">GUARDAR</button>
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

    <!-- MODAL NUEVO USUARIO -->
    <div x-show="modalUsuario" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-transition x-cloak>
        <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl border-t-[12px] border-green-500 overflow-hidden" @click.away="modalUsuario = false">
            <div class="p-10">
                <h3 class="text-3xl font-black text-gray-800 mb-2 uppercase italic">Nuevo Usuario</h3>
                <p class="text-[10px] font-black text-green-600 bg-green-50 px-3 py-1 rounded-full uppercase tracking-widest mb-6 inline-block italic border border-green-100">La contraseña será temporal</p>
                <form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nombre Completo</label>
                        <input type="text" name="name" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-green-500 outline-none font-bold" required placeholder="Ej. Juan Pérez">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Username (Login)</label>
                        <input type="text" name="username" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-green-500 outline-none font-bold" required placeholder="Ej. jperez1">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Rol en el Sistema</label>
                        <select name="role" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-green-500 outline-none font-bold">
                            <option value="mesero">Mesero</option>
                            <option value="cocina">Cocina</option>
                            <option value="admin">Administrador</option>
                            <option value="cajera">Cajera</option>
                        </select>
                    </div>
                    <div class="flex gap-4 pt-6">
                        <button type="button" @click="modalUsuario = false" class="flex-1 px-6 py-4 border-2 border-gray-100 rounded-2xl font-black text-gray-400 hover:bg-gray-50 uppercase tracking-widest transition">CANCELAR</button>
                        <button type="submit" class="flex-1 px-6 py-4 bg-green-500 text-white rounded-2xl font-black hover:scale-105 transition shadow-lg shadow-green-100 uppercase tracking-widest">CREAR USUARIO</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR USUARIO -->
    <div x-show="modalEditUsuario" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-transition x-cloak>
        <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl border-t-[12px] border-blue-500 overflow-hidden" @click.away="modalEditUsuario = false">
            <div class="p-10">
                <h3 class="text-3xl font-black text-gray-800 mb-2 uppercase italic">Editar Usuario</h3>
                <form :action="'{{ url('/admin/usuarios/actualizar') }}/' + userEditData.id" method="POST" class="space-y-4 mt-6">
                    @csrf
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nombre Completo</label>
                        <input type="text" name="name" x-model="userEditData.name" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 outline-none font-bold" required>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Username (Login)</label>
                        <input type="text" name="username" x-model="userEditData.username" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 outline-none font-bold" required>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nueva Contraseña (Opcional)</label>
                        <input type="password" name="password" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 outline-none font-bold" placeholder="Dejar en blanco para no cambiar">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Rol en el Sistema</label>
                        <select name="role" x-model="userEditData.role" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 outline-none font-bold">
                            <option value="mesero">Mesero</option>
                            <option value="cocina">Cocina</option>
                            <option value="admin">Administrador</option>
                            <option value="cajera">Cajera</option>
                        </select>
                    </div>
                    <div class="flex gap-4 pt-6">
                        <button type="button" @click="modalEditUsuario = false" class="flex-1 px-6 py-4 border-2 border-gray-100 rounded-2xl font-black text-gray-400 hover:bg-gray-50 uppercase tracking-widest transition">CANCELAR</button>
                        <button type="submit" class="flex-1 px-6 py-4 bg-blue-500 text-white rounded-2xl font-black hover:scale-105 transition shadow-lg shadow-blue-100 uppercase tracking-widest">ACTUALIZAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>