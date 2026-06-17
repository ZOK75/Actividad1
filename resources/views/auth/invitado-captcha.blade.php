<x-guest-layout>

    <div class="absolute top-0 right-0 p-6 text-end z-50 flex items-center gap-4">
     
        <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-950 dark:text-gray-400 dark:hover:text-white transition duration-150 ease-in-out">
            Log in
        </a>
        
        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="font-semibold text-gray-600 hover:text-gray-950 dark:text-gray-400 dark:hover:text-white transition duration-150 ease-in-out">
                Register
            </a>
        @endif
    </div>
    
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-gray-800 mb-2">Acceso de Invitados</h2>
        <p class="text-sm text-gray-600">Por favor introduce tu correo electrónico y verifica el reCAPTCHA para continuar.</p>
    </div>

    <form method="POST" action="{{ route('invitado.verify') }}">
        @csrf

        <div class="mb-4">
            <x-input-label for="invitado_email" value="Correo Electrónico" />
            <x-text-input id="invitado_email" class="block mt-1 w-full" type="email" name="invitado_email" required autofocus placeholder="correo@ejemplo.com" />
            <x-input-error :messages="$errors->get('invitado_email')" class="mt-2" />
        </div>

        <div class="mt-4 flex justify-center">
            {!! htmlFormSnippet() !!}
        </div>
        <div class="mt-2 text-center">
            <x-input-error :messages="$errors->get('g-recaptcha-response')" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center">
                Enviar Código de Verificación
            </x-primary-button>
        </div>
    </form>

    {!! htmlScriptTagJsApi() !!}
</x-guest-layout>