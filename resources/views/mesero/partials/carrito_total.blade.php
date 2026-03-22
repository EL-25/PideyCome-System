@if(isset($total) && $total > 0)
    <div class="animate-fade-in border-t-2 border-dashed border-gray-100 mt-6 pt-6">
        <div class="flex justify-between items-center mb-2">
            <span class="font-black text-gray-400 uppercase text-[10px] tracking-widest leading-none">Subtotal</span>
            <span class="text-sm font-bold text-gray-600 leading-none">${{ number_format($total, 2) }}</span>
        </div>
        
        <div class="flex justify-between items-end mb-6">
            <span class="font-black text-gray-950 uppercase text-xs tracking-tighter leading-none">Total de la Orden</span>
            <div class="text-right leading-none">
                <span class="text-4xl font-black text-orange-600 tracking-tighter block">
                    ${{ number_format($total, 2) }}
                </span>
            </div>
        </div>
    </div>
@else
    {{-- Espacio vacío para mantener el diseño cuando no hay nada --}}
    <div class="h-20 flex items-center justify-center border-t border-dashed border-gray-100 mt-6 pt-6 opacity-20">
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 italic">Esperando productos...</p>
    </div>
@endif
