<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Código OTP</title>
</head>
<body style="font-family: sans-serif; text-align: center; padding: 20px; background-color: #f4f4f7;">
    <div style="max-width: 500px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <h2 style="color: #333333;">Código de Seguridad</h2>
        <p style="color: #51545e;">Tu código de verificación para continuar como invitado es el siguiente:</p>
        
        <div style="background: #f0f4f8; padding: 15px; color: #1e40af; font-size: 32px; font-weight: bold; letter-spacing: 5px; display: inline-block; border-radius: 6px; margin: 20px 0; width: 200px;">
            {{ $otp }}
        </div>
        
        <p style="font-size: 12px; color: #9ca3af;">Si no solicitaste este código, puedes ignorar este correo de forma segura.</p>
    </div>
</body>
</html>