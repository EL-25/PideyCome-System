@foreach($productos as $producto)
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-xl hover:shadow-orange-50/50 transition-all duration-300 animate-fade-in">
        <div class="flex justify-between items-start mb-6">
            <div>
                <span class="px-3 py-1 bg-gray-50 text-[10px] font-black text-gray-400 rounded-full uppercase border border-gray-100">
                    {{ $producto->categoria }}
                </span>
                <h4 class="text-2xl font-bold mt-3 text-gray-900 leading-tight">{{ $producto->nombre }}</h4>
                
                {{-- Alerta de Stock Bajo --}}
                @if($producto->stock > 0 && $producto->stock < 10)
                    <div class="flex items-center gap-1 mt-1 text-orange-500 font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="text-[10px] uppercase">¡Solo quedan {{ $producto->stock }}!</span>
                    </div>
                @endif

                <p class="text-3xl font-black text-orange-600 mt-2">${{ number_format($producto->precio, 2) }}</p>
            </div>
            
            @if($producto->stock <= 0)
                <span class="text-[10px] bg-red-50 text-red-500 px-3 py-1.5 rounded-full font-black uppercase ring-1 ring-red-100">
                    Agotado
                </span>
            @endif
        </div>

        @if($producto->stock > 0)
            {{-- Agregamos un ID único al botón para manipularlo con JS --}}
            <button type="button" 
                    id="btn-add-{{ $producto->id }}"
                    onclick="agregarAlCarritoAjax('{{ $producto->id }}')" 
                    class="w-full bg-orange-500 text-white font-black py-4 rounded-2xl flex items-center justify-center gap-2 hover:bg-orange-600 shadow-lg shadow-orange-100 active:scale-[0.98] transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg> 
                Agregar
            </button>
        @else
            <button disabled class="w-full bg-gray-100 text-gray-400 font-black py-4 rounded-2xl flex items-center justify-center gap-2 cursor-not-allowed border border-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                No disponible
            </button>
        @endif
    </div>
@endforeach

@if($productos->isEmpty())
    <div class="col-span-full py-20 text-center bg-white rounded-[3rem] border-2 border-dashed border-gray-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-200 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">No hay productos en esta categoría</p>
    </div>
@endif
