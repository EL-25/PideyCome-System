<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PideYCome - Panel del Mesero</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
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
            <div class="flex items-center gap-8">
                <div class="flex items-center gap-3">
                    <div class="bg-orange-500 p-2 rounded-lg shadow-md shadow-orange-200">
                        <i data-lucide="utensils" class="text-white w-5 h-5"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-gray-900 leading-none">PideYCome</h1>
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ Auth::user()->name }} - Mesero</span>
                    </div>
                </div>

                <div class="hidden md:flex items-center bg-gray-100 p-1 rounded-xl gap-1">
                    <a href="{{ route('mesero.index') }}" class="px-6 py-2 rounded-lg text-sm font-black transition {{ Route::is('mesero.index') ? 'bg-white text-orange-600 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                        Nueva Orden
                    </a>
                    <a href="{{ route('mesero.ordenes') }}" class="px-6 py-2 rounded-lg text-sm font-black transition {{ Route::is('mesero.ordenes') ? 'bg-white text-orange-600 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                        Mis Órdenes
                    </a>
                </div>
            </div>

            <div class="flex items-center gap-6">
                {{-- Campanita de Notificaciones --}}
                <a href="{{ route('mesero.ordenes') }}" class="relative group cursor-pointer">
                    <i data-lucide="bell" class="w-6 h-6 text-gray-400 group-hover:text-orange-500 transition-colors"></i>
                    @if(isset($notificacionesCount) && $notificacionesCount > 0)
                        <span class="absolute -top-2 -right-2 bg-red-600 text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center border-2 border-white animate-bounce shadow-sm">
                            {{ $notificacionesCount }}
                        </span>
                    @endif
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-red-600 transition">
                        <i data-lucide="log-out" class="w-4 h-4"></i> Salir
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-4 lg:p-8">
        <header class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-gray-900 tracking-tight">Menú</h2>
                <p class="text-gray-500 mt-1 font-medium italic">"Atendiendo con excelencia"</p>
            </div>
            
            <div class="flex gap-3">
                <form action="{{ route('mesero.limpiar') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-gray-200/40 text-gray-500 px-8 py-3 rounded-full font-bold flex items-center gap-2 hover:bg-gray-200 transition">
                        <i data-lucide="trash-2" class="w-5 h-5"></i> Limpiar Orden
                    </button>
                </form>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
            
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white p-2 rounded-2xl shadow-sm border border-gray-100 inline-flex gap-1 overflow-x-auto max-w-full">
                    @foreach(['Todos', 'Comida', 'Bebidas', 'Postres'] as $cat)
                        <button type="button" 
                                onclick="filtrarCategoriaAjax('{{ $cat }}')"
                                id="btn-cat-{{ $cat }}"
                                class="btn-categoria px-8 py-2 rounded-xl font-bold transition whitespace-nowrap 
                                {{ $cat == 'Todos' ? 'bg-orange-500 text-white shadow-lg shadow-orange-100' : 'bg-white text-gray-400 hover:text-orange-500' }}">
                            {{ $cat }}
                        </button>
                    @endforeach
                </div>

                <div id="grid-productos" class="grid grid-cols-1 md:grid-cols-2 gap-6 relative min-h-[400px]">
                    @include('mesero.partials.productos_grid')
                </div>
            </div>

            <aside class="sticky top-28 lg:col-span-1">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-50 relative overflow-hidden">
                    <div id="carrito-loader" class="absolute inset-0 bg-white/50 z-10 flex items-center justify-center hidden">
                        <i data-lucide="loader-2" class="w-8 h-8 text-orange-500 animate-spin"></i>
                    </div>

                    <h3 class="text-2xl font-black mb-8 flex items-center gap-3">
                        <i data-lucide="shopping-basket" class="text-orange-500 w-8 h-8"></i> Orden Actual
                    </h3>

                    <form action="{{ route('pedido.store') }}" method="POST" class="space-y-8">
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
                                <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Cliente <span class="text-red-500">*</span></label>
                                <input type="text" id="input_cliente" name="cliente" placeholder="Nombre completo..." required 
                                       class="w-full mt-2 bg-gray-50 border-transparent rounded-2xl p-4 font-bold focus:bg-white focus:ring-2 focus:ring-orange-500 outline-none transition-all placeholder:text-gray-300">
                            </div>
                        </div>

                        <div id="carrito-items" class="mt-8 space-y-4 border-t border-gray-100 pt-6 max-h-96 overflow-y-auto">
                            @include('mesero.partials.carrito_lista')
                        </div>

                        <div id="carrito-total-container">
                             @include('mesero.partials.carrito_total')
                        </div>

                        <button id="btn-submit-orden" type="submit" @if(empty($carrito)) disabled @endif 
                                class="w-full bg-orange-600 text-white font-black py-5 rounded-[1.5rem] hover:bg-orange-700 shadow-xl shadow-orange-100 transition-all flex items-center justify-center gap-2 uppercase tracking-tighter disabled:bg-gray-200 disabled:shadow-none disabled:cursor-not-allowed">
                            <i data-lucide="send" class="w-5 h-5"></i> Enviar a Cocina
                        </button>
                    </form>
                </div>
            </aside>
        </div>
    </main>

    <script>
        lucide.createIcons();
        axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';

        // --- REFRESCAR NOTIFICACIONES CADA 30 SEGUNDOS ---
        setInterval(function() {
            const inputCliente = document.getElementById('input_cliente');
            const carritoItems = document.getElementById('carrito-items');
            
            // Solo recarga si el mesero no está escribiendo y el carrito no tiene cosas críticas
            // O si simplemente quieres que la campanita se actualice
            if (document.activeElement !== inputCliente) {
                console.log('Sincronizando órdenes listas...');
                window.location.reload();
            }
        }, 30000);

        // --- FILTRADO DE CATEGORÍAS AJAX ---
        function filtrarCategoriaAjax(categoria) {
            const grid = document.getElementById('grid-productos');
            grid.classList.add('opacity-50');

            document.querySelectorAll('.btn-categoria').forEach(btn => {
                btn.classList.remove('bg-orange-500', 'text-white', 'shadow-lg', 'shadow-orange-100');
                btn.classList.add('bg-white', 'text-gray-400');
            });

            const btnActivo = document.getElementById(`btn-cat-${categoria}`);
            btnActivo.classList.remove('bg-white', 'text-gray-400');
            btnActivo.classList.add('bg-orange-500', 'text-white', 'shadow-lg', 'shadow-orange-100');

            axios.get('{{ route("mesero.index") }}', { 
                params: { categoria: categoria },
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => {
                grid.innerHTML = response.data;
                lucide.createIcons();
            })
            .catch(error => console.error(error))
            .finally(() => grid.classList.remove('opacity-50'));
        }

        // --- FUNCIONES DEL CARRITO ---
        function agregarAlCarritoAjax(id) {
            const loader = document.getElementById('carrito-loader');
            loader.classList.remove('hidden');

            axios.post('{{ route("carrito.agregar.ajax") }}', { id: id })
                .then(response => {
                    actualizarCarritoUI(response.data);
                    showToast(response.data.message, 'success');
                })
                .catch(error => {
                    showToast(error.response.data.error || 'Error al agregar', 'error');
                })
                .finally(() => loader.classList.add('hidden'));
        }

        function actualizarCantidadAjax(id, accion) {
            axios.post('{{ route("carrito.actualizar.ajax") }}', { id: id, accion: accion })
                .then(response => actualizarCarritoUI(response.data));
        }

        function eliminarDelCarritoAjax(id) {
            axios.post('{{ route("carrito.eliminar.ajax") }}', { id: id })
                .then(response => {
                    actualizarCarritoUI(response.data);
                    showToast('Producto eliminado', 'info');
                });
        }

        function actualizarCarritoUI(data) {
            const container = document.getElementById('carrito-items');
            const totalContainer = document.getElementById('carrito-total-container');
            const btnSubmit = document.getElementById('btn-submit-orden');
            
            let html = '';
            if (Object.keys(data.carrito).length === 0) {
                html = `<div class="py-10 text-center text-gray-400 font-bold">Orden vacía</div>`;
                btnSubmit.disabled = true;
                totalContainer.innerHTML = '';
            } else {
                Object.values(data.carrito).forEach(item => {
                    html += `
                    <div class="flex flex-col bg-gray-50 p-4 rounded-2xl gap-3 border border-gray-100">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-bold text-gray-900 text-sm">${item.nombre}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase">${item.categoria}</p>
                            </div>
                            <button type="button" onclick="eliminarDelCarritoAjax(${item.id})" class="text-gray-300 hover:text-red-500">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2 bg-white rounded-full p-1 shadow-inner">
                                <button type="button" onclick="actualizarCantidadAjax(${item.id}, 'decrementar')" class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-orange-500">
                                    <i data-lucide="minus" class="w-4 h-4"></i>
                                </button>
                                <span class="font-black text-gray-900 text-sm w-5 text-center">${item.cantidad}</span>
                                <button type="button" onclick="actualizarCantidadAjax(${item.id}, 'incrementar')" class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-orange-500" ${item.cantidad >= item.stock_max ? 'disabled' : ''}>
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                </button>
                            </div>
                            <p class="font-black text-gray-950 text-base">$${(item.precio * item.cantidad).toFixed(2)}</p>
                        </div>
                    </div>`;
                });
                btnSubmit.disabled = false;
                totalContainer.innerHTML = `
                    <div class="border-t-2 border-dashed border-gray-100 mt-6 pt-6 flex justify-between items-end mb-4">
                        <span class="font-black text-gray-400 uppercase text-xs">Subtotal</span>
                        <span class="text-3xl font-black text-gray-900">$${data.total.toFixed(2)}</span>
                    </div>`;
            }
            container.innerHTML = html;
            lucide.createIcons();
        }

        function showToast(message, type) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const bg = type === 'success' ? 'bg-[#011612]' : (type === 'error' ? 'bg-red-900' : 'bg-[#0D1B2A]');
            const icon = type === 'success' ? 'check-circle' : (type === 'error' ? 'alert-circle' : 'info');
            const color = type === 'success' ? 'text-green-400' : (type === 'error' ? 'text-red-400' : 'text-blue-400');
            const border = type === 'success' ? 'border-green-500' : (type === 'error' ? 'border-red-500' : 'border-blue-500');

            toast.className = `flex items-center p-4 text-white ${bg} rounded-xl shadow-lg border-l-4 ${border} animate-fade-in transition-all`;
            toast.innerHTML = `<i data-lucide="${icon}" class="w-5 h-5 mr-3 ${color}"></i><span class="text-sm font-medium">${message}</span>`;
            
            container.appendChild(toast);
            lucide.createIcons();
            setTimeout(() => {
                toast.classList.add('opacity-0', 'scale-95');
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        }

        document.getElementById('tipo_orden').addEventListener('change', function() {
            document.getElementById('div_mesa').style.display = (this.value === 'para_llevar') ? 'none' : 'block';
        });
    </script>

    <style>
        .animate-fade-in { animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</body>
</html>
