<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensaje de Contacto</title>
    <style>
        <link rel="stylesheet" href="{{ asset('css/email.css') }}">
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            
            <!-- Cabecera / Identidad visual -->
            <div class="email-header">
                <h1>Nuevo Mensaje de Contacto</h1>
            </div>

            <!-- Cuerpo del mensaje / Datos dinámicos -->
            <div class="email-body">
                <p>Has recibido una nueva consulta a través del formulario de contacto.</p>
                
                <p><span class="field-label">Nombre:</span> {{ $data['name'] ?? 'No especificado' }}</p>
                <p><span class="field-label">Correo electrónico:</span> {{ $data['email'] ?? 'No especificado' }}</p>
                <p><span class="field-label">Asunto:</span> {{ $data['subject'] ?? 'Sin asunto' }}</p>

                <div class="message-card">
                    <span class="field-label">Mensaje:</span>
                    <p style="margin-bottom: 0;">{{ $data['message'] ?? '' }}</p>
                </div>
            </div>

            <!-- Pie institucional -->
            <div class="email-footer">
                <p>Este correo se envió automáticamente desde el sistema web institucional.</p>
                <p>&copy; {{ date('Y') }} Todos los derechos reservados.</p>
            </div>

        </div>
    </div>
</body>
</html>