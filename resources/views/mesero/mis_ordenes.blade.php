<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PideYCome - Mis Órdenes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-[#F8F9FA] text-gray-800 font-sans">

    <nav class="bg-white border-b border-gray-100 p-4 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-8">
                <div class="flex items-center gap-3">
                    <div class="bg-orange-500 p-2 rounded-lg shadow-md">
                        <i data-lucide="utensils" class="text-white w-5 h-5"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-gray-900 leading-none">PideYCome</h1>
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ Auth::user()->username }}</span>
                    </div>
                </div>

                <div class="hidden md:flex items-center bg-gray-100 p-1 rounded-xl gap-1">
                    <a href="{{ route('mesero.index') }}" class="px-6 py-2 rounded-lg text-sm font-black transition text-gray-400 hover:text-gray-600">
                        Nueva Orden
                    </a>
                    <a href="{{ route('mesero.ordenes') }}" class="px-6 py-2 rounded-lg text-sm font-black transition bg-white text-orange-600 shadow-sm">
                        Mis Órdenes
                    </a>
                </div>
            </div>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-red-600 transition">
                    <i data-lucide="log-out" class="w-4 h-4"></i> Salir
                </button>
            </form>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-4 lg:p-8">
        <header class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-gray-900 tracking-tight">Estado de Pedidos</h2>
                <p class="text-gray-500 mt-1 font-medium italic">"Monitorea tus órdenes en tiempo real"</p>
            </div>
            
            <div class="relative min-w-[200px]">
                <select id="filtro-mesa" onchange="filtrarPorMesaAjax(this.value)" class="w-full bg-white border border-gray-200 rounded-2xl p-4 font-bold focus:ring-2 focus:ring-orange-500 outline-none appearance-none shadow-sm">
                    <option value="Todas las órdenes">Todas las órdenes</option>
                    @for ($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}">Mesa {{ $i }}</option>
                    @endfor
                </select>
                <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5 pointer-events-none"></i>
            </div>
        </header>

        <div id="contenedor-pedidos" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @include('mesero.partials.pedidos_lista')
        </div>
    </main>

    <script>
        lucide.createIcons();

        function filtrarPorMesaAjax(mesa) {
            const container = document.getElementById('contenedor-pedidos');
            container.classList.add('opacity-50');

            axios.get('{{ route("mesero.ordenes") }}', {
                params: { mesa: mesa },
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => {
                container.innerHTML = response.data;
                lucide.createIcons();
            })
            .catch(error => console.error(error))
            .finally(() => container.classList.remove('opacity-50'));
        }

        // Auto-actualización cada 30 segundos para ver cambios de cocina
        setInterval(() => {
            const mesaActual = document.getElementById('filtro-mesa').value;
            filtrarPorMesaAjax(mesaActual);
        }, 30000);
    </script>
</body>
</html>
