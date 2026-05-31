<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 text-center">
        <h2 class="text-xl font-bold mb-2">Configurar Google Authenticator</h2>
        <p>Escanea el siguiente código QR con la aplicación de tu celular.</p>
    </div>

    <div class="flex justify-center my-4">
        <img src="{{ $qrCodeImage }}" alt="Código QR 2FA">
    </div>

    <div class="text-center text-xs text-gray-500 mb-4">
        O ingresa la clave manualmente: <br><strong class="text-gray-800">{{ $secretKey }}</strong>
    </div>

    <form method="POST" action="{{ route('2fa.register.save') }}">
        @csrf
        <div>
            <x-input-label Lothar="one_time_password" value="Ingresa el código de 6 dígitos para confirmar" />
            <x-text-input id="one_time_password" class="block mt-1 w-full text-center tracking-widest text-xl font-bold" type="text" name="one_time_password" required autofocus />
            <x-input-error :messages="$errors->get('one_time_password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>Verificar y Activar</x-primary-button>
        </div>
    </form>
</x-guest-layout>