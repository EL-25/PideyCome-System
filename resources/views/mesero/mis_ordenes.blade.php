<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PideYCome - Mis Órdenes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        orange: {
                            50: '#FFF7ED',
                            100: '#FFEDD5',
                            200: '#FED7AA',
                            300: '#FDBA74',
                            400: '#FB923C',
                            500: '#E05E1A',
                            600: '#C24B10',
                            700: '#9A3412',
                            800: '#7C2D12',
                            900: '#431407',
                        }
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body {
            background-color: #E5E7EB !important;
        }
    </style>
</head>
<body class="bg-[#F8F9FA] text-gray-800 font-sans">

    <nav class="bg-white border-b border-gray-100 p-4 sticky top-0 z-50 shadow-sm" x-data="{ mobileMenu: false }">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-4 md:gap-8">
                <div class="flex items-center gap-3">
                    <div class="bg-orange-500 p-2 rounded-lg shadow-md">
                        <i data-lucide="utensils" class="text-white w-5 h-5"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-gray-900 leading-none text-sm md:text-base">Restaurante UDB</h1>
                        <span class="text-[9px] md:text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ Auth::user()->name }}</span>
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
            
            <div class="flex items-center gap-4">
                <button @click="mobileMenu = !mobileMenu" class="md:hidden text-gray-400">
                    <i data-lucide="menu" x-show="!mobileMenu"></i>
                    <i data-lucide="x" x-show="mobileMenu"></i>
                </button>

                <form action="{{ route('logout') }}" method="POST" class="hidden md:block">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-red-600 transition">
                        <i data-lucide="log-out" class="w-4 h-4"></i> Salir
                    </button>
                </form>
            </div>
        </div>

        <!-- Mobile Nav -->
        <div x-show="mobileMenu" x-cloak class="md:hidden mt-4 pt-4 border-t border-gray-100 space-y-2">
            <a href="{{ route('mesero.index') }}" class="block px-4 py-3 rounded-xl font-bold {{ Route::is('mesero.index') ? 'bg-orange-50 text-orange-600' : 'text-gray-500' }}">Nueva Orden</a>
            <a href="{{ route('mesero.ordenes') }}" class="block px-4 py-3 rounded-xl font-bold {{ Route::is('mesero.ordenes') ? 'bg-orange-50 text-orange-600' : 'text-gray-500' }}">Mis Órdenes</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-3 text-red-500 font-bold flex items-center gap-2">
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
            
            <div class="flex flex-col md:flex-row items-center gap-3">
                <div class="relative min-w-[200px] w-full">
                    <select id="filtro-mesa" onchange="filtrarPorMesaAjax(this.value)" class="w-full bg-white border border-gray-200 rounded-2xl p-4 font-bold focus:ring-2 focus:ring-orange-500 outline-none appearance-none shadow-sm">
                        <option value="Todas las órdenes">Todas las órdenes</option>
                        @for ($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ request('mesa') == $i ? 'selected' : '' }}>Mesa {{ $i }}</option>
                        @endfor
                    </select>
                    <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5 pointer-events-none"></i>
                </div>

                <div id="btn-cerrar-cuenta-container" class="hidden">
                    <form id="form-solicitar-cuenta" action="" method="POST">
                        @csrf
                        <button type="submit" class="bg-gray-900 text-white px-8 py-4 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-gray-200 flex items-center gap-2 whitespace-nowrap">
                            <i data-lucide="receipt" class="w-5 h-5"></i>
                            Cerrar Cuenta y enviar a Caja
                        </button>
                    </form>
                </div>
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
            const btnContainer = document.getElementById('btn-cerrar-cuenta-container');
            const formCerrar = document.getElementById('form-solicitar-cuenta');
            
            container.classList.add('opacity-50');

            // Mostrar/Ocultar botón de cerrar cuenta
            if (mesa !== 'Todas las órdenes') {
                btnContainer.classList.remove('hidden');
                formCerrar.action = `/mesero/solicitar-cuenta/${mesa}`;
            } else {
                btnContainer.classList.add('hidden');
            }

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

        // Al cargar, si ya hay una mesa seleccionada, mostramos el botón
        window.addEventListener('DOMContentLoaded', () => {
            const mesaInicial = document.getElementById('filtro-mesa').value;
            if (mesaInicial !== 'Todas las órdenes') {
                filtrarPorMesaAjax(mesaInicial);
            }
        });

        // Auto-actualización cada 1.5 segundos para ver cambios de cocina en tiempo real
        setInterval(() => {
            const mesaActual = document.getElementById('filtro-mesa').value;
            filtrarPorMesaAjax(mesaActual);
        }, 1500);
    </script>
</body>
</html>
