<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PideYCome - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F2E7DC] h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-96 border-t-8 border-[#F28705]">
        
        <div class="flex flex-col items-center mb-6">
            <img src="{{ asset('img/PideYCome.png') }}" alt="Logo PideYCome" class="w-40 h-auto mb-4">
            <h2 class="text-3xl font-bold text-[#F28705] text-center tracking-tight">PideYCome</h2>
        </div>
        
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
                <label class="block text-gray-700 font-semibold mb-2">Usuario</label>
                <input type="text" name="username" value="{{ old('username') }}" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F28705]" 
                       placeholder="Ej: mesero1">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Contraseña</label>
                <input type="password" name="password" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F28705]" 
                       placeholder="••••••••">
            </div>

            <button type="submit" 
                    class="w-full bg-[#F28705] text-white font-bold py-3 rounded-lg hover:bg-[#d67604] transition duration-300 shadow-md">
                ENTRAR
            </button>
        </form>
    </div> </body>
</html>
