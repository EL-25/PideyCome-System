<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PideYCome - Panel del Mesero</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-[#F8F9FA] text-gray-800 font-sans">

    <div id="toast-container" class="fixed top-5 right-5 z-[100] space-y-3 w-80">
        @if(session('info'))
        <div class="flex items-center p-4 text-white bg-[#0D1B2A] rounded-xl shadow-lg border-l-4 border-blue-500 animate-fade-in">
            <i data-lucide="info" class="w-5 h-5 mr-3 text-blue-400"></i>
            <span class="text-sm font-medium">{{ session('info') }}</span>
        </div>
        @endif

        @if(session('success'))
        <div class="flex items-center p-4 text-white bg-[#011612] rounded-xl shadow-lg border-l-4 border-green-500 animate-fade-in">
            <i data-lucide="check-circle" class="w-5 h-5 mr-3 text-green-400"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
        @endif
    </div>

    <nav class="bg-white border-b border-gray-100 p-4 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-orange-500 p-2 rounded-lg shadow-md shadow-orange-200">
                    <i data-lucide="utensils" class="text-white w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="font-bold text-gray-900 leading-none">Sistema Restaurante</h1>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ Auth::user()->username }} - Mesero</span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-red-600 border border-transparent px-4 py-2 transition">
                        <i data-lucide="log-out" class="w-4 h-4"></i> Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-4 lg:p-8">
        <header class="mb-10">
            <h2 class="text-4xl font-black text-gray-900 tracking-tight">Panel del Mesero</h2>
            <p class="text-gray-500 mt-1 font-medium">Mesero: {{ Auth::user()->username }}</p>
            
            <div class="flex gap-3 mt-8">
                <a href="{{ route('mesero.index') }}" class="bg-white border border-gray-100 px-8 py-3 rounded-full font-bold flex items-center gap-2 shadow-sm text-orange-600 ring-1 ring-gray-100">
                    <i data-lucide="plus" class="w-5 h-5"></i> Nueva Orden
                </a>
                <form action="{{ route('carrito.limpiar') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-gray-200/40 text-gray-500 px-8 py-3 rounded-full font-bold flex items-center gap-2 hover:bg-gray-200 transition">
                        <i data-lucide="trash-2" class="w-5 h-5"></i> Limpiar Carrito
                    </button>
                </form>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
            
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white p-2 rounded-2xl shadow-sm border border-gray-100 inline-flex gap-1">
                    @foreach(['Todos', 'Comida', 'Bebidas', 'Postres'] as $cat)
                        <a href="{{ route('mesero.index', ['categoria' => $cat]) }}" 
                           class="px-8 py-2 rounded-xl font-bold transition {{ (request('categoria', 'Todos') == $cat) ? 'bg-gray-50 text-gray-900 shadow-sm' : 'bg-white text-gray-400 hover:text-orange-500' }}">
                            {{ $cat }}
                        </a>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($productos as $producto)
                        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-xl hover:shadow-orange-50/50 transition-all duration-300">
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <span class="px-3 py-1 bg-gray-50 text-[10px] font-black text-gray-400 rounded-full uppercase tracking-tighter border border-gray-100">
                                        {{ $producto->categoria }}
                                    </span>
                                    <h4 class="text-2xl font-bold mt-3 text-gray-900 leading-tight">{{ $producto->nombre }}</h4>
                                    <p class="text-3xl font-black text-orange-600 mt-2">${{ number_format($producto->precio, 2) }}</p>
                                </div>
                                
                                @if($producto->stock <= 0)
                                    <span class="text-[10px] bg-red-50 text-red-500 px-3 py-1.5 rounded-full font-black uppercase ring-1 ring-red-100">
                                        Agotado
                                    </span>
                                @endif
                            </div>

                            @if($producto->stock > 0)
                                <form action="{{ route('carrito.agregar', $producto->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-orange-500 text-white font-black py-4 rounded-2xl flex items-center justify-center gap-2 hover:bg-orange-600 shadow-lg shadow-orange-100 active:scale-[0.98] transition-all">
                                        <i data-lucide="plus" class="w-6 h-6"></i> Agregar
                                    </button>
                                </form>
                            @else
                                <button disabled class="w-full bg-orange-100/50 text-orange-200 font-black py-4 rounded-2xl flex items-center justify-center gap-2 cursor-not-allowed">
                                    <i data-lucide="plus" class="w-6 h-6 text-white"></i> No Disponible
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

           <aside class="sticky top-28">
    <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-50">
        <h3 class="text-2xl font-black mb-8 flex items-center gap-3">
            <i data-lucide="shopping-basket" class="text-orange-500 w-8 h-8"></i> Orden Actual
        </h3>

        <form action="#" method="POST" class="space-y-8">
            @csrf
            
            <div class="space-y-5">
                <div>
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Tipo de Orden</label>
                    <select id="tipo_orden" name="tipo_orden" class="w-full mt-2 bg-gray-50 border-transparent rounded-2xl p-4 font-bold focus:bg-white focus:ring-2 focus:ring-orange-500 outline-none transition-all">
                        <option value="comer_aqui">Comer Aquí</option>
                        <option value="para_llevar">Para Llevar</option>
                    </select>
                </div>

                <div id="div_mesa">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Mesa</label>
                    <select name="mesa_id" class="w-full mt-2 bg-gray-50 border-transparent rounded-2xl p-4 font-bold focus:bg-white focus:ring-2 focus:ring-orange-500 outline-none transition-all">
                        <option value="">Selecciona una mesa</option>
                        @for ($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">Mesa {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Nombre del Cliente (Opcional)</label>
                    <input type="text" name="cliente" placeholder="Escribe el nombre aquí..." 
                           class="w-full mt-2 bg-gray-50 border-transparent rounded-2xl p-4 font-bold focus:bg-white focus:ring-2 focus:ring-orange-500 outline-none transition-all placeholder:text-gray-300">
                </div>
            </div>

            <div class="mt-8 space-y-4 border-t border-gray-100 pt-6">
                @php $total = 0; @endphp
                @forelse($carrito as $id => $item)
                    @php $total += $item['precio'] * $item['cantidad']; @endphp
                    <div class="flex justify-between items-center group">
                        <div class="flex items-center gap-3">
                            <span class="bg-orange-100 text-orange-600 px-2 py-1 rounded-lg text-xs font-black">{{ $item['cantidad'] }}x</span>
                            <div>
                                <p class="font-bold text-gray-800 text-sm">{{ $item['nombre'] }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $item['categoria'] }}</p>
                            </div>
                        </div>
                        <p class="font-black text-gray-900">${{ number_format($item['precio'] * $item['cantidad'], 2) }}</p>
                    </div>
                @empty
                    <div class="py-10 flex flex-col items-center justify-center text-gray-300">
                        <i data-lucide="shopping-cart" class="w-12 h-12 mb-4 opacity-10"></i>
                        <p class="font-bold text-gray-400">Carrito vacío</p>
                    </div>
                @endforelse
            </div>

            @if($total > 0)
            <div class="border-t-2 border-dashed border-gray-100 pt-6 flex justify-between items-end mb-4">
                <span class="font-black text-gray-400 uppercase text-xs tracking-widest">Total a pagar</span>
                <span class="text-3xl font-black text-gray-900">${{ number_format($total, 2) }}</span>
            </div>
            @endif

            <button type="submit" {{ $total == 0 ? 'disabled' : '' }} 
                    class="w-full bg-orange-500 text-white font-black py-5 rounded-[1.5rem] hover:bg-orange-600 shadow-xl shadow-orange-100 transition-all flex items-center justify-center gap-2 uppercase tracking-tighter disabled:bg-gray-200 disabled:shadow-none disabled:cursor-not-allowed">
                Enviar a Cocina
            </button>
        </form>
    </div>
</aside>
        </div>
    </main>

    <script>
        lucide.createIcons();

        const tipoOrdenSelect = document.getElementById('tipo_orden');
        const divMesa = document.getElementById('div_mesa');

        tipoOrdenSelect.addEventListener('change', function() {
            if (this.value === 'para_llevar') {
                divMesa.classList.add('hidden');
            } else {
                divMesa.classList.remove('hidden');
            }
        });

        document.querySelectorAll('#toast-container > div').forEach(toast => {
            setTimeout(() => {
                toast.classList.add('opacity-0', 'scale-95', 'transition-all', 'duration-500');
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        });
    </script>

    <style>
        .animate-fade-in {
            animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>
</body>
</html>
