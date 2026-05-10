<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Contraseña - PideYCome</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-[#F2E7DC] min-h-screen flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-md rounded-[3rem] shadow-2xl border-t-[12px] border-[#F28705] overflow-hidden p-10">
        <div class="text-center mb-8">
            <div class="bg-[#F28705] w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-orange-100">
                <i data-lucide="shield-check" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-3xl font-black text-gray-800 uppercase italic leading-none">Seguridad</h1>
            <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px] mt-2">Debes actualizar tu contraseña temporal</p>
        </div>

        <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nueva Contraseña</label>
                <div class="relative">
                    <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-300"></i>
                    <input type="password" name="password" required 
                        class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-[#F28705] outline-none font-bold transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Confirmar Contraseña</label>
                <div class="relative">
                    <i data-lucide="check-circle" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-300"></i>
                    <input type="password" name="password_confirmation" required 
                        class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-[#F28705] outline-none font-bold transition">
                </div>
            </div>

            @if($errors->any())
            <div class="bg-red-50 p-4 rounded-2xl border border-red-100">
                <p class="text-red-500 text-xs font-bold text-center italic">{{ $errors->first() }}</p>
            </div>
            @endif

            <button type="submit" 
                class="w-full bg-[#F28705] text-white py-5 rounded-[2rem] font-black uppercase tracking-widest shadow-xl shadow-orange-100 hover:scale-[1.02] active:scale-95 transition-all">
                ACTUALIZAR Y ENTRAR
            </button>
        </form>

        <p class="text-center text-gray-300 text-[10px] font-black uppercase tracking-[0.2em] mt-10 italic">PideYCome System v1.0</p>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
