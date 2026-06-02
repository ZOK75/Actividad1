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
            <x-input-label for="one_time_password" value="1. Ingresa el código de 6 dígitos de la App celular" />
            <x-text-input id="one_time_password" class="block mt-1 w-full text-center tracking-widest text-xl font-bold" type="text" name="one_time_password" required autofocus placeholder="000000" maxlength="6" />
            <x-input-error :messages="$errors->get('one_time_password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email_otp_code" class="text-blue-600 font-semibold" value="2. Ingresa el código OTP enviado a tu Correo" />
            <x-text-input id="email_otp_code" class="block mt-1 w-full text-center tracking-widest text-xl font-bold border-blue-300 focus:border-blue-500 focus:ring-blue-500" type="text" name="email_otp_code" required placeholder="000000" maxlength="6" />
            <x-input-error :messages="$errors->get('email_otp_code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="w-full justify-center bg-blue-600 hover:bg-blue-700">
                Verificar y Activar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>