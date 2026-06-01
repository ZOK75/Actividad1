<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-gray-800 mb-2">Verificación de Seguridad</h2>
        <p class="text-sm text-gray-600">Para continuar, primero verificaremos que no eres un robot</p>
    </div>

    <form method="POST" action="{{ route('invitado.verify') }}">
        @csrf

        <div class="mt-4 flex justify-center">
            {!! htmlFormSnippet() !!}
        </div>
        
        <div class="mt-2 text-center">
            <x-input-error :messages="$errors->get('g-recaptcha-response')" />
        </div>

        <div class="flex items-center justify-center mt-6">
            <x-primary-button class="w-full justify-center py-3 text-lg">
                Continuar
            </x-primary-button>
        </div>
    </form>

    {!! htmlScriptTagJsApi() !!}
</x-guest-layout>