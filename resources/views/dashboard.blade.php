<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @auth
                @if(auth()->user()->is_admin)
                    <div class="bg-red-600 text-white text-center font-bold p-3 rounded-lg shadow-md my-4">
                        Modo ADMINISTRADOR. Estas accediendo a contenido PRIVADO, en caso de estar viendo esto y no contar con las los permisos necesarios, se tomarán ACCIONES LEGALES.
                    </div>
                @endif
            @endauth

            @guest
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded-lg shadow-sm mb-6 font-medium" role="alert">
                    Iniciaste como invitado. Para ver más funciones inicia sesión o regístrate.
                </div>
            @endguest

            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @auth
                        {{ __("You're logged in!") }}
                    @else
                        Bienvenido al sistema. Por favor, inicia sesión para realizar operaciones.
                    @endauth
                </div>
            </div>
        </div>
    </div>
@if(!auth()->check())
    <script>
        if (window.name !== 'pestaña_invitado_activa') {
            
            if (sessionStorage.getItem('flujo_invitado_valido') === 'true') {
                window.name = 'pestaña_invitado_activa';
                sessionStorage.removeItem('flujo_invitado_valido');
            } else {
                window.location.href = "{{ route('invitado.checkpoint') }}";
            }
        }
    </script>
@endif
    
</x-app-layout>