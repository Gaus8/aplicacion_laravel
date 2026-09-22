<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Correo Electrónico | Sistema Auth</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .form-container {
            max-width: 650px;
            margin: 0 auto;
            background-color: #111827;
            padding: 30px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #9ca3af;
            font-size: 14px;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 11px 15px;
            background-color: #1f2937;
            border: 1px solid #374151;
            border-radius: 8px;
            color: #ffffff;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .file-dropzone {
            border: 2px dashed #374151;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background-color: #1f2937;
            cursor: pointer;
            transition: border-color 0.2s ease;
        }
        .file-dropzone:hover {
            border-color: #3b82f6;
        }
        .file-dropzone input[type="file"] {
            display: none;
        }
        .file-dropzone-label {
            color: #9ca3af;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        .file-dropzone-label svg {
            color: #6b7280;
        }
        .file-preview-list {
            margin-top: 10px;
            font-size: 12px;
            color: #60a5fa;
            text-align: left;
            word-break: break-all;
        }
        .btn-group {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 25px;
        }
        .btn-secondary {
            background-color: #374151;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }
        .btn-secondary:hover {
            background-color: #4b5563;
        }
        .btn-submit {
            background-color: #2563eb;
            color: #ffffff;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .btn-submit:hover {
            background-color: #1d4ed8;
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
            <p class="page-subtitle">Diligencia la información y adjunta archivos para enviarlos usando la plantilla del sistema.</p>
        </div>

        <div class="form-container">
            <!-- NOTA: enctype necesario para enviar archivos -->
            <form action="{{ route('admin.media.sendEmail') }}" method="POST" enctype="multipart/form-data">
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

                <!-- Campo para adjuntar múltiples archivos -->
                <div class="form-group">
                    <label>Archivos Adjuntos (opcional)</label>
                    <div class="file-dropzone" onclick="document.getElementById('attachments').click()">
                        <label for="attachments" class="file-dropzone-label">
                            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            <span>Haz clic para seleccionar o arrastra tus archivos aquí</span>
                            <span style="font-size: 11px; color: #6b7280;">(Imágenes, PDF, DOCX, ZIP - máx. 10MB por archivo)</span>
                        </label>
                        <input type="file" name="attachments[]" id="attachments" multiple onchange="updateFileNames(this)">
                    </div>
                    <div id="file-list" class="file-preview-list"></div>
                </div>

                <div class="btn-group">
                    <a href="{{ route('dashboard') }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-submit">
                        Enviar Correo
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function updateFileNames(input) {
            const fileList = document.getElementById('file-list');
            fileList.innerHTML = '';
            if (input.files.length > 0) {
                const names = Array.from(input.files).map(f => `📎 ${f.name} (${(f.size / 1024 / 1024).toFixed(2)} MB)`).join('<br>');
                fileList.innerHTML = names;
            }
        }
    </script>

</body>
</html>