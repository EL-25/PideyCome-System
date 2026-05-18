<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Contraseña - PideYCome</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        orange: {
                            50: '#FFF7ED',
                            100: '#FFEDD5',
                            200: '#FED7AA',
                            300: '#FDBA74',
                            400: '#FB923C',
                            500: '#E05E1A',
                            600: '#C24B10',
                            700: '#9A3412',
                            800: '#7C2D12',
                            900: '#431407',
                        }
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            background-color: #E5E7EB !important;
        }
    </style>
</head>
<body class="bg-[#F3F4F6] min-h-screen flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-md rounded-[3rem] shadow-2xl border-t-[12px] border-[#E05E1A] overflow-hidden p-10" x-data="{ show1: false, show2: false }">
        <div class="text-center mb-8">
            <div class="bg-[#E05E1A] w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-orange-100">
                <i data-lucide="shield-check" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight leading-none">Seguridad</h1>
            <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px] mt-2">Debes actualizar tu contraseña temporal</p>
        </div>

        <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nueva Contraseña</label>
                <div class="relative">
                    <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-300"></i>
                    <input :type="show1 ? 'text' : 'password'" name="password" required 
                        class="w-full pl-12 pr-12 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-[#E05E1A] outline-none font-bold transition">
                    <button type="button" @click="show1 = !show1" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#E05E1A]">
                        <i x-show="!show1" data-lucide="eye" class="w-4 h-4"></i>
                        <i x-show="show1" data-lucide="eye-off" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Confirmar Contraseña</label>
                <div class="relative">
                    <i data-lucide="check-circle" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-300"></i>
                    <input :type="show2 ? 'text' : 'password'" name="password_confirmation" required 
                        class="w-full pl-12 pr-12 py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-[#E05E1A] outline-none font-bold transition">
                    <button type="button" @click="show2 = !show2" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#E05E1A]">
                        <i x-show="!show2" data-lucide="eye" class="w-4 h-4"></i>
                        <i x-show="show2" data-lucide="eye-off" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            @if($errors->any())
            <div class="bg-red-50 p-4 rounded-2xl border border-red-100">
                <p class="text-red-500 text-xs font-bold text-center italic">{{ $errors->first() }}</p>
            </div>
            @endif

            <button type="submit" 
                class="w-full bg-[#E05E1A] text-white py-5 rounded-[2rem] font-black uppercase tracking-widest shadow-xl shadow-orange-100 hover:scale-[1.02] active:scale-95 transition-all">
                ACTUALIZAR Y ENTRAR
            </button>
        </form>

        <p class="text-center text-gray-300 text-[10px] font-black uppercase tracking-[0.2em] mt-10 italic">PideYCome System v1.0</p>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
