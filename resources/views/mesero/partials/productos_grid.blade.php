@foreach($productos as $producto)
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-xl hover:shadow-orange-50/50 transition-all duration-300">
        <div class="flex justify-between items-start mb-6">
            <div>
                <span class="px-3 py-1 bg-gray-50 text-[10px] font-black text-gray-400 rounded-full uppercase border border-gray-100">
                    {{ $producto->categoria }}
                </span>
                <h4 class="text-2xl font-bold mt-3 text-gray-900 leading-tight">{{ $producto->nombre }}</h4>
                
                @if($producto->stock > 0 && $producto->stock < 10)
                    <div class="flex items-center gap-1 mt-1 text-orange-500 font-bold">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i>
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
            <button type="button" onclick="agregarAlCarritoAjax('{{ $producto->id }}')" class="w-full bg-orange-500 text-white font-black py-4 rounded-2xl flex items-center justify-center gap-2 hover:bg-orange-600 shadow-lg shadow-orange-100 active:scale-[0.98] transition-all">
                <i data-lucide="plus" class="w-6 h-6"></i> Agregar
            </button>
        @else
            <button disabled class="w-full bg-gray-100 text-gray-400 font-black py-4 rounded-2xl flex items-center justify-center gap-2 cursor-not-allowed border border-gray-200">
                <i data-lucide="slash" class="w-5 h-5"></i> No disponible
            </button>
        @endif
    </div>
@endforeach

@if($productos->isEmpty())
    <div class="col-span-full py-20 text-center">
        <i data-lucide="search-x" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
        <p class="text-gray-500 font-bold">No se encontraron productos en esta categoría.</p>
    </div>
@endif
