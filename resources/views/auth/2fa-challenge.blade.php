<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 text-center">
        <h2 class="text-xl font-bold mb-2">Verificación de Doble Factor</h2>
        <p>Por seguridad corporativa, introduce tus dos llaves de acceso dinámicas.</p>
    </div>

    <form method="POST" action="{{ route('2fa.challenge.verify') }}">
        @csrf
        
        <div>
            <x-input-label for="one_time_password" value="Código Google Authenticator (Celular)" />
            <x-text-input id="one_time_password" class="block mt-1 w-full text-center tracking-widest text-xl font-bold" type="text" name="one_time_password" placeholder="000000" maxlength="6" required autofocus />
            <x-input-error :messages="$errors->get('one_time_password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email_otp_code" class="text-blue-600 font-semibold" value="Código de Seguridad (Enviado a tu Correo)" />
            <x-text-input id="email_otp_code" class="block mt-1 w-full text-center tracking-widest text-xl font-bold border-blue-300 focus:border-blue-500 focus:ring-blue-500" type="text" name="email_otp_code" placeholder="000000" maxlength="6" required />
            <x-input-error :messages="$errors->get('email_otp_code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="w-full justify-center bg-blue-600 hover:bg-blue-700">
                Verificar Identidad
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>