<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Acceso a PDI App</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">

    <img src="{{ $message->embed($logoHeader) }}" style="width: 50%; height: auto; margin-bottom: 2%;" alt="Logo"><br>

    <p>Estimado usuario,</p>

    <p>
        Nos complace informarle que le hemos concedido acceso a la aplicación <strong>PDI App</strong> con las siguientes credenciales:
    </p>

    <p><strong>Usuario:</strong> {{ $employeeData['username'] }}</p>
    <p><strong>Contraseña:</strong> {{ env('ACCESS_DEFAULT_PWD') }}</p>

    <p>Le recomendamos cambiar su contraseña después de su primer inicio de sesión por motivos de seguridad.</p>

    <p>Si tiene alguna pregunta o necesita asistencia adicional, no dude en ponerse en contacto con nosotros.</p>

    <p style="margin-top: 2%;">Saludos cordiales.</p>

    <hr style="margin: 30px 0; border: 0; border-top: 1px solid #ccc;">

    <!-- Firma -->
    <table width="100%" style="max-width: 600px; margin: auto; font-size: 12px;">
        <tr>
            <td width="30%" align="center">
                <a href="https://www.grupoicot.es/">
                    <img src="{{ $message->embed($logoIcot) }}" style="width: 100%; max-width: 120px; height: auto;" alt="Logo ICOT">
                </a>
            </td>
            <td width="70%" style="padding-left: 15px;">
                <p><strong style="color:#B01C2E;">Sonia Castro Martel</strong><br>
                <span style="color: #595959;">Dpto. Informática</span><br>
                <span style="color: #595959;">C/ Pio XII, 62 – 35006 Las Palmas G.C.</span><br>
                <a href="mailto:desarrollo@grupoicot.es" style="color:#B01C2E;">desarrollo@grupoicot.es</a><br>
                <a href="http://www.grupoicot.es/" style="color:#B01C2E;">www.grupoicot.es</a></p>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="padding-top: 10px;">
                <a href="https://www.facebook.com/grupoicot">
                    <img src="{{ $message->embed($logoFacebook) }}" style="width: 24px; margin-right: 8px;" alt="Facebook">
                </a>
                <a href="https://www.instagram.com/hospital_icot_telde/">
                    <img src="{{ $message->embed($logoInstagram) }}" style="width: 24px; margin-right: 8px;" alt="Instagram">
                </a>
                <a href="https://www.youtube.com/user/ICOTgruposanitario">
                    <img src="{{ $message->embed($logoYoutube) }}" style="width: 24px; margin-right: 8px;" alt="YouTube">
                </a>
                <a href="https://www.linkedin.com/company/grupo-icot/">
                    <img src="{{ $message->embed($logoLinkedin) }}" style="width: 24px;" alt="LinkedIn">
                </a>
            </td>
        </tr>
    </table>

</body>
</html>
