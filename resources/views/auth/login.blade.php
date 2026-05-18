<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PideYCome - Login</title>
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
<body class="bg-[#F3F4F6] h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-96 border-t-8 border-[#E05E1A]" x-data="{ show: false }">
        
        <div class="flex flex-col items-center mb-6">
            <img src="{{ asset('img/PideYCome.png') }}" alt="Logo PideYCome" class="w-40 h-auto mb-4">
            <h2 class="text-3xl font-bold text-[#E05E1A] text-center tracking-tight">PideYCome</h2>
        </div>
        
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 text-sm font-bold">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-sm">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-black text-xs uppercase tracking-widest mb-2 ml-1">Usuario</label>
                <input type="text" name="username" value="{{ old('username') }}" 
                       class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-xl focus:border-[#E05E1A] outline-none font-bold" 
                       placeholder="Usuario">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-black text-xs uppercase tracking-widest mb-2 ml-1">Contraseña</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" name="password" 
                           class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-xl focus:border-[#E05E1A] outline-none font-bold" 
                           placeholder="••••••••">
                    <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#E05E1A] transition-colors">
                        <i x-show="!show" data-lucide="eye" class="w-5 h-5"></i>
                        <i x-show="show" data-lucide="eye-off" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <button type="submit" 
                    class="w-full bg-[#E05E1A] text-white font-black py-4 rounded-xl hover:bg-[#c24b10] transition duration-300 shadow-lg shadow-orange-100 uppercase tracking-widest">
                INICIAR SESIÓN
            </button>
        </form>
    </div>

    <script>
        lucide.createIcons();
        window.onpageshow = function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        };
    </script>

</body>
</html>
