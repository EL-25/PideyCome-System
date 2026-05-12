<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PideYCome - Administración</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #F28705; border-radius: 10px; }
        @keyframes fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fade-in 0.4s ease-out; }
    </style>
</head>
<body class="bg-[#F2E7DC] min-h-screen font-sans" x-data="{ 
        tab: '{{ request('tab', 'productos') }}', 
        search: '', 
        modalProducto: false,
        modalEditProducto: false,
        modalUsuario: false,
        modalEditUsuario: false,
        editData: { id: '', nombre: '', precio: '', categoria: '', stock: '' },
        userEditData: { id: '', name: '', username: '', role: '' }
    }">

    <nav class="bg-white shadow-sm border-b border-gray-200 px-4 md:px-6 py-3 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="bg-[#F28705] p-2 rounded-lg">
                    <i data-lucide="utensils" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <p class="font-bold text-gray-800 leading-none text-sm md:text-base">Restaurante UDB</p>
                    <p class="text-[9px] md:text-[10px] text-gray-500 uppercase font-black tracking-widest">{{ auth()->user()->name }} - Admin</p>
                </div>
            </div>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="flex items-center gap-2 px-4 py-1.5 border-2 border-gray-100 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 transition">
                    <i data-lucide="log-out" class="w-4 h-4"></i> <span class="hidden md:inline">Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto p-6">
        
        <div class="flex items-center gap-4 mb-8">
            <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100">
                <i data-lucide="layout-dashboard" class="w-8 h-8 text-[#F28705]"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Panel Administrativo</h1>
                <p class="text-gray-500 font-medium italic">Control total del sistema</p>
            </div>
        </div>

        <!-- Alertas -->
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
             class="fixed top-24 right-6 z-50 bg-green-500 text-white px-6 py-4 rounded-2xl shadow-2xl font-bold flex items-center gap-3 animate-fade-in border-b-4 border-green-700">
            <i data-lucide="check-circle" class="w-6 h-6"></i>
            {{ session('success') }}
        </div>
        @endif

        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            <div class="flex flex-wrap bg-white p-1 rounded-2xl border border-gray-200 shadow-sm w-full md:w-auto">
                <a href="{{ route('admin.index') }}" 
                   class="flex-1 md:flex-none px-6 py-2 rounded-xl text-sm font-black transition duration-200 uppercase tracking-tighter {{ Route::is('admin.index') ? 'bg-[#F28705] text-white' : 'text-gray-500 hover:bg-gray-50' }}">
                    General
                </a>
                <a href="{{ route('admin.ventas') }}" 
                   class="flex-1 md:flex-none px-6 py-2 rounded-xl text-sm font-black transition duration-200 uppercase tracking-tighter {{ Route::is('admin.ventas') ? 'bg-[#F28705] text-white' : 'text-gray-500 hover:bg-gray-50' }}">
                    Ventas
                </a>
                <a href="{{ route('admin.movimientos') }}" 
                   class="flex-1 md:flex-none px-6 py-2 rounded-xl text-sm font-black transition duration-200 uppercase tracking-tighter {{ Route::is('admin.movimientos') ? 'bg-[#F28705] text-white' : 'text-gray-500 hover:bg-gray-50' }}">
                    Auditoría
                </a>
                <a href="{{ route('admin.usuarios') }}" 
                   class="flex-1 md:flex-none px-6 py-2 rounded-xl text-sm font-black transition duration-200 uppercase tracking-tighter {{ Route::is('admin.usuarios') ? 'bg-[#F28705] text-white' : 'text-gray-500 hover:bg-gray-50' }}">
                    Usuarios
                </a>
            </div>

            <div class="relative w-full md:w-80">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input type="text" x-model="search" placeholder="Buscar..."
                    class="w-full pl-12 pr-4 py-3 bg-white border-2 border-transparent rounded-2xl focus:border-[#F28705] outline-none shadow-sm font-bold text-sm transition">
            </div>
        </div>

        <div class="min-h-[500px]">
            @yield('admin_content')
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>