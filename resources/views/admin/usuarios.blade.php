@extends('admin.layout')

@section('admin_content')
<div class="space-y-8 animate-fade-in">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-black text-gray-900 tracking-tighter uppercase">Gestión de Personal</h2>
            <p class="text-gray-500 font-medium italic">Administra los accesos y roles del sistema</p>
        </div>
        <button @click="modalUsuario = true" class="bg-green-600 text-white px-8 py-3 rounded-2xl font-black flex items-center gap-2 hover:scale-105 transition shadow-lg shadow-green-100 uppercase tracking-widest text-xs">
            <i data-lucide="user-plus" class="w-5 h-5"></i> Nuevo Usuario
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($usuarios as $u)
        <div x-show="'{{ strtolower($u->name) }}'.includes(search.toLowerCase())" 
            class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-xl hover:border-green-500 transition-all group relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                <button @click="modalEditUsuario = true; userEditData = {id: '{{ $u->id }}', name: '{{ $u->name }}', username: '{{ $u->username }}', role: '{{ $u->role }}'}" 
                    class="p-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition shadow-sm">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </button>
                <form action="{{ route('admin.usuarios.destroy', $u->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar a este usuario?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition shadow-sm">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>

            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-gray-900 rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-lg uppercase tracking-tighter">
                    {{ substr($u->name, 0, 1) }}
                </div>
                <div>
                    <h3 class="font-black text-gray-900 text-lg uppercase leading-tight">{{ $u->name }}</h3>
                    <span class="inline-block bg-gray-100 text-gray-500 text-[10px] px-3 py-1 rounded-full font-black uppercase tracking-widest border border-gray-200 mt-1">{{ $u->role }}</span>
                </div>
            </div>

            <div class="space-y-3 pt-4 border-t border-dashed border-gray-100">
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Username</span>
                    <span class="text-sm font-bold text-gray-700">{{ $u->username }}</span>
                </div>
                @if($u->temp_passwd)
                <div class="flex justify-center pt-2">
                    <span class="text-[9px] font-black text-orange-600 bg-orange-50 px-4 py-1.5 rounded-full uppercase tracking-tighter italic border border-orange-100">Contraseña Temporal Pendiente</span>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- MODALS USUARIOS -->
<div x-show="modalUsuario" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-transition x-cloak>
    <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl border-t-[12px] border-green-500 overflow-hidden" @click.away="modalUsuario = false">
        <div class="p-10">
            <h3 class="text-3xl font-black text-gray-800 mb-2 uppercase italic">Nuevo Usuario</h3>
            <p class="text-[10px] font-black text-green-600 bg-green-50 px-3 py-1 rounded-full uppercase tracking-widest mb-6 inline-block italic border border-green-100">La contraseña será temporal</p>
            <form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nombre Completo</label>
                    <input type="text" name="name" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-green-500 outline-none font-bold shadow-inner" required placeholder="Ej. Juan Pérez">
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Username (Login)</label>
                    <input type="text" name="username" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-green-500 outline-none font-bold shadow-inner" required placeholder="Ej. jperez1">
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Rol en el Sistema</label>
                    <select name="role" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-green-500 outline-none font-bold shadow-inner">
                        <option value="mesero">Mesero</option>
                        <option value="cocina">Cocina</option>
                        <option value="admin">Administrador</option>
                        <option value="cajera">Cajera</option>
                    </select>
                </div>
                <div class="flex gap-4 pt-6">
                    <button type="button" @click="modalUsuario = false" class="flex-1 px-6 py-4 border-2 border-gray-100 rounded-2xl font-black text-gray-400 hover:bg-gray-50 uppercase tracking-widest transition">CANCELAR</button>
                    <button type="submit" class="flex-1 px-6 py-4 bg-green-500 text-white rounded-2xl font-black hover:scale-105 transition shadow-lg shadow-green-100 uppercase tracking-widest">CREAR USUARIO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div x-show="modalEditUsuario" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-transition x-cloak>
    <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl border-t-[12px] border-blue-500 overflow-hidden" @click.away="modalEditUsuario = false">
        <div class="p-10">
            <h3 class="text-3xl font-black text-gray-800 mb-2 uppercase italic">Editar Usuario</h3>
            <form :action="'{{ url('/admin/usuarios/actualizar') }}/' + userEditData.id" method="POST" class="space-y-4 mt-6">
                @csrf
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nombre Completo</label>
                    <input type="text" name="name" x-model="userEditData.name" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 outline-none font-bold shadow-inner" required>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Username (Login)</label>
                    <input type="text" name="username" x-model="userEditData.username" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 outline-none font-bold shadow-inner" required>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nueva Contraseña (Opcional)</label>
                    <input type="password" name="password" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 outline-none font-bold shadow-inner" placeholder="Dejar en blanco para no cambiar">
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Rol en el Sistema</label>
                    <select name="role" x-model="userEditData.role" class="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:border-blue-500 outline-none font-bold shadow-inner">
                        <option value="mesero">Mesero</option>
                        <option value="cocina">Cocina</option>
                        <option value="admin">Administrador</option>
                        <option value="cajera">Cajera</option>
                    </select>
                </div>
                <div class="flex gap-4 pt-6">
                    <button type="button" @click="modalEditUsuario = false" class="flex-1 px-6 py-4 border-2 border-gray-100 rounded-2xl font-black text-gray-400 hover:bg-gray-50 uppercase tracking-widest transition">CANCELAR</button>
                    <button type="submit" class="flex-1 px-6 py-4 bg-blue-500 text-white rounded-2xl font-black hover:scale-105 transition shadow-lg shadow-blue-100 uppercase tracking-widest">ACTUALIZAR</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
