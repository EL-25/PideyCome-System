@forelse($mesasActivas as $mesa_id => $pedidos)
    @php 
        $totalMesa = $pedidos->sum('total');
        $esParaLlevar = str_starts_with($mesa_id, 'Llevar-');
        $nombreMesa = $esParaLlevar ? 'Para Llevar' : 'Mesa ' . $mesa_id;
        $cliente = $pedidos->first()->cliente;
    @endphp

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-3">
            <span class="text-[10px] font-bold uppercase {{ $esParaLlevar ? 'text-orange-500 bg-orange-50' : 'text-blue-500 bg-blue-50' }} px-2 py-1 rounded-full">
                {{ $esParaLlevar ? 'Llevar' : 'Local' }}
            </span>
        </div>

        <div class="mb-4">
            <h3 class="text-xl font-bold text-gray-800">{{ $nombreMesa }}</h3>
            <p class="text-sm text-gray-500 font-medium">{{ $cliente }}</p>
        </div>

        <div class="space-y-1 mb-6">
            <div class="flex justify-between text-sm text-gray-400">
                <span>Órdenes:</span>
                <span class="font-bold text-gray-700">{{ $pedidos->count() }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-400 text-sm">Total:</span>
                <span class="text-2xl font-black text-emerald-600">${{ number_format($totalMesa, 2) }}</span>
            </div>
        </div>

        <a href="{{ route('cajera.cuenta', $mesa_id) }}" 
           class="block w-full bg-emerald-600 hover:bg-emerald-700 text-white text-center py-3 rounded-xl font-bold transition-colors shadow-lg shadow-emerald-100">
            Ver Cuenta
        </a>
    </div>
@empty
    <div class="col-span-full py-20 text-center">
        <div class="bg-white inline-block p-6 rounded-full mb-4 shadow-sm border border-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-500">No hay cuentas activas</h2>
        <p class="text-gray-400">Todo está al día en caja.</p>
    </div>
@endforelse
