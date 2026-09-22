<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Imagen | Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/subirImagen.css') }}">
</head>
<body>

    <!-- Navegación Superior -->
    <header class="navbar">
        <div class="navbar-container">
            <div class="brand-wrapper">
                <div class="brand-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 2 2 002-2v-6a2 2 2 00-2-2H6a2 2 2 00-2 2v6a2 2 2 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <span class="brand-title">Sistema Auth</span>
            </div>

            <a href="{{ route('dashboard') }}" class="btn-back-nav">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Dashboard
            </a>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="main-container">
        
        <div class="form-card">
            
            <div class="card-header">
                <div class="header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="card-title">Subir Nueva Imagen</h1>
                <p class="card-subtitle">Ingresa los datos requeridos para guardar tu archivo multimedia</p>
            </div>

            <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Campo Nombre -->
                <div class="form-group">
                    <label for="name" class="form-label">Nombre del archivo</label>
                    <input type="text" class="form-input" id="name" name="name" placeholder="Ej. Imagen de perfil" required>
                </div>

                <!-- Campo Archivo (Dropzone) -->
                <div class="form-group">
                    <label class="form-label">Seleccionar archivo</label>
                    
                    <div class="upload-dropzone">
                        <input type="file" class="dropzone-file-input" id="file-input" name="file" accept="image/*" required>
                        <svg class="dropzone-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="dropzone-text"><span>Haz clic para seleccionar</span> o arrastra un archivo</p>
                        <p class="dropzone-hint">Formatos soportados: PNG, JPG, JPEG, WEBP</p>
                    </div>

                    <!-- Vista previa de la imagen -->
                    <div class="preview-container" id="preview-wrapper">
                        <img id="preview" src="#" alt="Vista previa" class="preview-image">
                    </div>
                </div>

                <!-- Botones -->
                <div class="actions-group">
                    <a href="{{ route('dashboard') }}" class="btn-cancel">Cancelar</a>
                    <button type="submit" class="btn-submit">Subir archivo</button>
                </div>
            </form>

        </div>

    </main>

    <!-- Script para la previsualización -->
    <script>
        document.getElementById('file-input').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const previewWrapper = document.getElementById('preview-wrapper');
            const preview = document.getElementById('preview');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewWrapper.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                previewWrapper.style.display = 'none';
            }
        });
    </script>
</body>
</html>