<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 text-center">
        <h2 class="text-xl font-bold mb-2">Verificación de Doble Factor</h2>
        <p>Abre tu aplicación Google Authenticator e ingresa el código dinámico de 6 dígitos.</p>
    </div>

    <form method="POST" action="{{ route('2fa.challenge.verify') }}">
        @csrf
        <div>
            <x-input-label for="one_time_password" value="Código OTP" />
            <x-text-input id="one_time_password" class="block mt-1 w-full text-center tracking-widest text-xl font-bold" type="text" name="one_time_password" placeholder="000000" maxlength="6" required autofocus />
            <x-input-error :messages="$errors->get('one_time_password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>Verificar Identidad</x-primary-button>
        </div>
    </form>
</x-guest-layout>