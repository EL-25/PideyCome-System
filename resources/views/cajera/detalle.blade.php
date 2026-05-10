<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Cuenta - PideYCome</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('cajera.index') }}" class="bg-white p-2 rounded-lg border border-gray-200 text-gray-500 hover:text-gray-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900">
                    {{ str_starts_with($mesa_id, 'Llevar-') ? 'Orden para Llevar' : 'Cuenta Mesa ' . $mesa_id }}
                </h1>
                <p class="text-emerald-600 font-bold">Cliente: {{ $cliente }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Detalle de Productos --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                        <h2 class="font-bold text-gray-800">Resumen de Consumo</h2>
                    </div>
                    <div class="p-0">
                        <table class="w-full">
                            <thead class="bg-gray-50 text-[10px] uppercase font-bold text-gray-400">
                                <tr>
                                    <th class="px-6 py-3 text-left">Producto</th>
                                    <th class="px-6 py-3 text-center">Cant.</th>
                                    <th class="px-6 py-3 text-right">Precio</th>
                                    <th class="px-6 py-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($pedidos as $pedido)
                                    @foreach($pedido->detalles as $detalle)
                                        <tr class="text-sm">
                                            <td class="px-6 py-4 font-medium text-gray-800">{{ $detalle->producto_nombre }}</td>
                                            <td class="px-6 py-4 text-center text-gray-500">{{ $detalle->cantidad }}</td>
                                            <td class="px-6 py-4 text-right text-gray-500">${{ number_format($detalle->precio, 2) }}</td>
                                            <td class="px-6 py-4 text-right font-bold text-gray-800">${{ number_format($detalle->cantidad * $detalle->precio, 2) }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-emerald-50/50">
                                    <td colspan="3" class="px-6 py-4 text-right font-bold text-emerald-800">TOTAL A PAGAR:</td>
                                    <td class="px-6 py-4 text-right text-xl font-black text-emerald-600">${{ number_format($totalGeneral, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Formulario de Pago --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl border border-emerald-100 p-6 sticky top-8">
                    <h2 class="text-lg font-black text-gray-900 mb-6">Procesar Pago</h2>
                    
                    <form id="form-pago" action="{{ route('cajera.pagar', $mesa_id) }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Método de Pago</label>
                            <div class="grid grid-cols-1 gap-2">
                                <label class="flex items-center p-3 border rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                                    <input type="radio" name="metodo_pago" value="efectivo" class="text-emerald-600" checked onchange="toggleMetodo('efectivo')">
                                    <span class="ml-3 text-sm font-bold text-gray-700">Efectivo</span>
                                </label>
                                <label class="flex items-center p-3 border rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                                    <input type="radio" name="metodo_pago" value="tarjeta" class="text-emerald-600" onchange="toggleMetodo('tarjeta')">
                                    <span class="ml-3 text-sm font-bold text-gray-700">Tarjeta de Débito/Crédito</span>
                                </label>
                            </div>
                        </div>

                        <div id="seccion_efectivo">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Efectivo Recibido</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                                <input type="number" name="monto_pagado" id="monto_pagado" step="0.01" value="{{ $totalGeneral }}" 
                                       class="w-full pl-8 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl font-bold text-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl border border-dashed border-gray-200 mt-4">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Cambio:</span>
                                    <span id="cambio_display" class="font-black text-lg text-emerald-600">$0.00</span>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="validarYProcesar()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl font-black text-lg shadow-lg shadow-emerald-200 transition-all active:scale-[0.98]">
                            FINALIZAR Y COBRAR
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE ANIMACIÓN DE TARJETA -->
    <div id="modal-tarjeta" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-[2.5rem] p-10 max-w-sm w-full text-center shadow-2xl transform scale-90 transition-transform duration-300" id="modal-content">
            <div id="anim-icon" class="mb-6 flex justify-center">
                <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center animate-bounce">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
            </div>
            <h2 id="modal-titulo" class="text-2xl font-black text-gray-900 mb-2">Procesar Tarjeta</h2>
            <p id="modal-subtitulo" class="text-gray-500 mb-8">Por favor, inserte o acerque la tarjeta al terminal.</p>
            
            <div id="modal-footer" class="space-y-3">
                <button onclick="confirmarPagoTarjeta()" class="w-full bg-emerald-600 text-white py-4 rounded-2xl font-black hover:bg-emerald-700 transition-all">
                    CONFIRMAR PAGO
                </button>
                <button onclick="cerrarModal()" class="w-full text-gray-400 font-bold py-2">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    <script>
        const total = {{ $totalGeneral }};
        const inputPago = document.getElementById('monto_pagado');
        const cambioDisplay = document.getElementById('cambio_display');
        const modal = document.getElementById('modal-tarjeta');
        const modalContent = document.getElementById('modal-content');

        function toggleMetodo(metodo) {
            document.getElementById('seccion_efectivo').style.display = (metodo === 'efectivo') ? 'block' : 'none';
        }

        inputPago.addEventListener('input', () => {
            const pago = parseFloat(inputPago.value) || 0;
            const cambio = pago - total;
            cambioDisplay.innerText = `$${Math.max(0, cambio).toFixed(2)}`;
            cambioDisplay.className = (cambio < 0) ? 'font-black text-lg text-red-500' : 'font-black text-lg text-emerald-600';
        });

        function validarYProcesar() {
            const metodo = document.querySelector('input[name="metodo_pago"]:checked').value;
            if (metodo === 'tarjeta') {
                abrirModal();
            } else {
                if (parseFloat(inputPago.value) < total) {
                    alert('El monto recibido es menor al total.');
                    return;
                }
                document.getElementById('form-pago').submit();
            }
        }

        function abrirModal() {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.add('opacity-100');
                modalContent.classList.add('scale-100');
            }, 10);
        }

        function cerrarModal() {
            modal.classList.remove('opacity-100');
            modalContent.classList.remove('scale-100');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        function confirmarPagoTarjeta() {
            // Cambiamos el estado a "Procesando"
            document.getElementById('modal-titulo').innerText = "Confirmando Pago...";
            document.getElementById('modal-subtitulo').innerText = "Estamos validando con el banco, no cierre esta ventana.";
            document.getElementById('modal-footer').innerHTML = `
                <div class="flex justify-center py-4">
                    <div class="w-10 h-10 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
                </div>
            `;
            
            // Simulación de respuesta de banco (2 segundos)
            setTimeout(() => {
                document.getElementById('anim-icon').innerHTML = `
                    <div class="w-20 h-20 bg-emerald-600 text-white rounded-full flex items-center justify-center animate-scale-in">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                `;
                document.getElementById('modal-titulo').innerText = "¡Pago Exitoso!";
                document.getElementById('modal-subtitulo').innerText = "La cuenta ha sido cancelada en su totalidad.";
                
                setTimeout(() => {
                    document.getElementById('form-pago').submit();
                }, 1500);
            }, 2000);
        }
    </script>

    <style>
        @keyframes scale-in { from { transform: scale(0); } to { transform: scale(1); } }
        .animate-scale-in { animation: scale-in 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    </style>
</body>
</html>
