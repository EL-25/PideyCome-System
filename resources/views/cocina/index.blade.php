<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PideYCome - Panel de Cocina</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-[#F8F9FA]">

    <div id="toast-container" class="fixed top-5 right-5 z-[100] space-y-3 w-80"></div>

    <nav class="bg-white border-b border-gray-100 py-4 mb-6 sticky top-0 z-50">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="bg-orange-500 text-white p-2 rounded-lg font-black shadow-md shadow-orange-100">PYC</span>
                <span class="font-bold text-gray-800 uppercase tracking-wider text-sm">Panel Cocina</span>
            </div>
            <div class="flex items-center gap-4">
                <div id="sync-indicator" class="flex items-center gap-2 text-[10px] font-bold text-green-500 uppercase">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    En línea
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-2">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <div class="bg-orange-500 p-2 rounded-lg text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Órdenes</h1>
                    <p id="count-label" class="text-sm text-gray-500">{{ $pedidos->count() }} activas ahora</p>
                </div>
            </div>
            
            <nav class="flex space-x-2 bg-gray-200 p-1 rounded-xl mt-4 md:mt-0">
                @php 
                    $currentTab = request('tab', 'todas');
                    $tabs = ['todas' => 'Todas', 'nuevas' => 'Nuevas', 'recibidas' => 'Recibidas', 'preparando' => 'En Preparación']; 
                @endphp
                @foreach($tabs as $key => $label)
                    <a href="{{ route('cocina.index', ['tab' => $key]) }}" 
                       class="px-4 py-2 rounded-lg text-[12px] font-bold uppercase transition-all {{ $currentTab == $key ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Grid de Pedidos --}}
        <div id="grid-pedidos" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @include('cocina.partials.pedidos_cards') {{-- Es recomendable mover las cards a un partial --}}
        </div>
    </div>

    <script>
        // Configuración Global de Axios
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';

        function avanzarEstado(pedidoId) {
            const btn = document.getElementById(`btn-avanzar-${pedidoId}`);
            if(btn) btn.disabled = true;

            axios.post(`/cocina/avanzar/${pedidoId}`)
                .then(response => {
                    showToast(response.data.message || 'Estado actualizado', 'success');
                    // Actualizamos la vista sin recargar
                    recargarGrid();
                })
                .catch(error => {
                    console.error(error);
                    showToast('Error al actualizar pedido', 'error');
                    if(btn) btn.disabled = false;
                });
        }

        function recargarGrid() {
            const grid = document.getElementById('grid-pedidos');
            const tab = new URLSearchParams(window.location.search).get('tab') || 'todas';

            axios.get('{{ route("cocina.index") }}', { params: { tab: tab } })
                .then(response => {
                    grid.innerHTML = response.data;
                    // Actualizar contador si el servidor lo envía en los headers o si lo extraes del HTML
                })
                .catch(error => console.error('Error al sincronizar:', error));
        }

        function showToast(message, type) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const bg = type === 'success' ? 'bg-[#011612]' : 'bg-red-900';
            const border = type === 'success' ? 'border-green-500' : 'border-red-500';

            toast.className = `flex items-center p-4 text-white ${bg} rounded-xl shadow-lg border-l-4 ${border} animate-fade-in transition-all`;
            toast.innerHTML = `<span class="text-sm font-medium">${message}</span>`;
            
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('opacity-0', 'scale-95');
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }

        // Sincronización silenciosa cada 30 segundos (Sin recargar la página)
        setInterval(recargarGrid, 30000);
    </script>
</body>
</html>
