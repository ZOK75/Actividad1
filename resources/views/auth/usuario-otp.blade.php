<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-blue-600 mb-2">🔒 Seguridad de la Cuenta</h2>
        <h3 class="text-md font-semibold text-gray-800 mb-2">Verificar Inicio de Sesión</h3>
        <p class="text-sm text-gray-600">Por seguridad, hemos enviado un código de acceso de 6 dígitos a tu correo registrado. Revisa tu bandeja de <strong>Mailtrap</strong>.</p>
    </div>

    <form method="POST" action="{{ route('usuario.otp.submit') }}">
        @csrf

        <div>
            <x-input-label for="otp_input" value="Código de Seguridad (6 dígitos)" />
            <x-text-input id="otp_input" class="block mt-1 w-full text-center text-2xl font-bold tracking-widest" type="text" name="otp_input" maxlength="6" required autofocus placeholder="000000"/>
            <x-input-error :messages="$errors->get('otp_input')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center bg-blue-600 hover:bg-blue-700">
                Verificar e Ingresar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>