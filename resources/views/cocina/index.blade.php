<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PideYCome - Panel de Cocina</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F8F9FA]">

    <nav class="bg-white border-b border-gray-100 py-4 mb-6">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="bg-orange-500 text-white p-2 rounded-lg font-black">PYC</span>
                <span class="font-bold text-gray-800 uppercase tracking-wider text-sm">Panel Cocina</span>
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
    </nav>

    <div class="container mx-auto px-4 py-2">
        
        {{-- Encabezado y Pestañas --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <div class="bg-orange-500 p-2 rounded-lg text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Órdenes</h1>
                    <p class="text-sm text-gray-500">{{ $pedidos->count() }} activas ahora</p>
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

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-xl shadow-sm flex justify-between items-center animate-bounce">
                <span class="font-medium">✅ {{ session('success') }}</span>
            </div>
        @endif

        {{-- Grid de Pedidos --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($pedidos as $pedido)
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col">
                    <div class="p-5 border-b border-gray-50">
                        <div class="flex justify-between items-start mb-2">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase {{ $pedido->estado == 'ordenada' ? 'bg-orange-100 text-orange-600' : ($pedido->estado == 'recibida' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600') }}">
                                {{ $pedido->estado == 'ordenada' ? 'Nueva' : ($pedido->estado == 'recibida' ? 'Recibida' : 'Preparando') }}
                            </span>
                            <span class="text-gray-400 text-[10px] font-bold uppercase">
                                {{ $pedido->created_at->diffForHumans(null, true) }}
                            </span>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 uppercase">MESA {{ $pedido->mesa_id ?? 'S/N' }}</h3>
                        <p class="text-sm text-red-500 font-bold">Cliente: {{ $pedido->cliente }}</p>
                    </div>

                    <div class="p-5 flex-grow space-y-2">
                        @foreach($pedido->detalles as $detalle)
                            <div class="flex items-center justify-between bg-gray-50 p-3 rounded-xl border border-gray-100">
                                <span class="font-bold text-gray-700 text-sm"><span class="text-orange-500">{{ $detalle->cantidad }}x</span> {{ $detalle->producto_nombre }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-5 pt-0 mt-auto">
                        <form action="{{ route('cocina.avanzar', $pedido->id) }}" method="POST">
                            @csrf
                            @if($pedido->estado == 'ordenada')
                                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg transition-all text-xs uppercase tracking-widest">
                                    📥 Recibir Orden
                                </button>
                            @elseif($pedido->estado == 'recibida')
                                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 rounded-xl shadow-lg transition-all text-xs uppercase tracking-widest">
                                    🔥 Iniciar Preparación
                                </button>
                            @elseif($pedido->estado == 'preparando')
                                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded-xl shadow-lg transition-all text-xs uppercase tracking-widest">
                                    ✅ Despachar Orden
                                </button>
                            @endif
                        </form>
                        <p class="text-[10px] text-center text-gray-400 mt-3 font-medium italic">
                            Mesero: {{ $pedido->mesero->name ?? 'Sistema' }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-24 bg-white rounded-3xl border-2 border-dashed border-gray-100">
                    <h2 class="text-xl font-bold text-gray-300 uppercase tracking-tighter">Sin órdenes pendientes</h2>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        setInterval(function() {
            console.log('Sincronizando cocina...');
            window.location.reload();
        }, 30000); 
    </script>
</body>
</html>
