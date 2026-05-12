<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PideYCome - Panel de Cajera</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#F3F4F6]">

    <nav class="bg-white border-b border-gray-100 py-4 mb-6 shadow-sm sticky top-0 z-50" x-data="{ mobileMenu: false }">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="bg-emerald-600 text-white p-2 rounded-lg font-black shadow-md shadow-emerald-100">PYC</span>
                <span class="font-bold text-gray-800 uppercase tracking-wider text-xs md:text-sm">Caja Central</span>
            </div>
            
            <div class="flex items-center gap-4">
                <div id="sync-indicator" class="hidden md:flex items-center gap-2 text-[10px] font-bold text-emerald-500 uppercase">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    En vivo
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4">
        
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900">Mesas Activas</h1>
            <p class="text-gray-500">Cuentas pendientes de pago</p>
        </div>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl relative mb-6 font-bold flex items-center gap-2 shadow-sm transition-all duration-500">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div id="grid-mesas" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @include('cajera.partials.mesas_grid')
        </div>
    </div>

    <script>
        lucide.createIcons();

        function pollMesas() {
            axios.get('{{ route('cajera.index') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => {
                    document.getElementById('grid-mesas').innerHTML = response.data;
                    lucide.createIcons();
                })
                .catch(err => console.error('Error polling mesas:', err));
        }

        setInterval(pollMesas, 5000); // Cada 5 segundos
    </script>
    </div>

</body>
</html>
