<x-guest-layout>
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