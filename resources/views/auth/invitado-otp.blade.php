<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-gray-800 mb-2">Verificar Código OTP</h2>
        <p class="text-sm text-gray-600">Hemos enviado un código de seguridad al correo electrónico proporcionado. Revisa tu bandeja de <strong>Mailtrap</strong>.</p>
    </div>

    <form method="POST" action="{{ route('invitado.otp.submit') }}" onsubmit="sessionStorage.setItem('flujo_invitado_valido', 'true');">
        @csrf

        <div>
            <x-input-label for="otp_input" value="Código OTP (6 dígitos)" />
            <x-text-input id="otp_input" class="block mt-1 w-full text-center text-2xl font-bold tracking-widest" type="text" name="otp_input" maxlength="6" required autofocus placeholder="000000"/>
            <x-input-error :messages="$errors->get('otp_input')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center">
                Ingresar al Dashboard
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>