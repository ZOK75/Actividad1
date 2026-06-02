<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @auth
                @if(auth()->user()->is_admin)
                    <div class="bg-red-600 text-white text-center font-bold p-3 rounded-lg shadow-md my-4">
                        Estás en modo administrador
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
        // Si la pestaña no está marcada como autorizada
        if (window.name !== 'pestaña_invitado_activa') {
            
            // Verificamos si viene directo de completar exitosamente el formulario del OTP
            if (sessionStorage.getItem('flujo_invitado_valido') === 'true') {
                // Como viene del flujo legal, bautizamos la pestaña para que resista los F5
                window.name = 'pestaña_invitado_activa';
                sessionStorage.removeItem('flujo_invitado_valido');
            } else {
                // Si no tiene nombre y tampoco viene del OTP... ¡Es una pestaña nueva/clonada!
                // Lo mandamos al checkpoint sin pensarlo
                window.location.href = "{{ route('invitado.checkpoint') }}";
            }
        }
    </script>
@endif
    
</x-app-layout>