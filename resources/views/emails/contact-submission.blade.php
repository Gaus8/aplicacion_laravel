<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Nuevo mensaje de contacto</title></head>
<body>
    <h1>Nuevo mensaje desde el formulario de contacto</h1>
    <p><strong>Nombre:</strong> {{ $submission->name }}</p>
    <p><strong>Correo:</strong> {{ $submission->email }}</p>
    <p><strong>Asunto:</strong> {{ $submission->subject }}</p>
    <p><strong>Mensaje:</strong></p>
    <p style="white-space: pre-line">{{ $submission->message }}</p>
</body>
</html>
