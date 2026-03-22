@php 
    $total = collect($carrito)->sum(fn($item) => $item['precio'] * $item['cantidad']); 
@endphp

@if($total > 0)
    <div class="border-t-2 border-dashed border-gray-100 mt-6 pt-6 flex justify-between items-end mb-4 animate-fade-in">
        <span class="font-black text-gray-400 uppercase text-xs tracking-widest">Subtotal</span>
        <span class="text-3xl font-black text-gray-900">${{ number_format($total, 2) }}</span>
    </div>
@endif
