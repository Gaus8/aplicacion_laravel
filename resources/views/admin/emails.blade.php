<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Correo Electrónico | Sistema Auth</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #111827;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #9ca3af;
            font-size: 14px;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            background-color: #1f2937;
            border: 1px solid #374151;
            border-radius: 6px;
            color: #ffffff;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
        }
        .btn-group {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        .btn-secondary {
            background-color: #374151;
            color: #ffffff;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <header class="navbar">
        <div class="navbar-container">
            <div class="brand-wrapper">
                <span class="brand-title">Sistema Auth</span>
            </div>
            <div class="user-nav">
                <a href="{{ route('dashboard') }}" class="btn-secondary">Volver al Dashboard</a>
            </div>
        </div>
    </header>

    <main class="main-container">
        <div class="page-header">
            <h1 class="page-title">Enviar Correo Electrónico</h1>
            <p class="page-subtitle">Diligencia la información para emitir un correo utilizando la plantilla del sistema.</p>
        </div>

        <div class="form-container">
            <form action="{{ route('admin.media.sendEmail') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="recipient">Correo Destinatario</label>
                    <input type="email" name="recipient" id="recipient" class="form-control" required placeholder="ejemplo@correo.com">
                </div>

                <div class="form-group">
                    <label for="subject">Asunto</label>
                    <input type="text" name="subject" id="subject" class="form-control" required placeholder="Asunto del correo">
                </div>

                <div class="form-group">
                    <label for="message">Mensaje</label>
                    <textarea name="message" id="message" rows="5" class="form-control" required placeholder="Escribe el contenido del mensaje..."></textarea>
                </div>

                <div class="btn-group">
                    <a href="{{ route('dashboard') }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-upload" style="border:none; cursor:pointer;">
                        Enviar Correo
                    </button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>