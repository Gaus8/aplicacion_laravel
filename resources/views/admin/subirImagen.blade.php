<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Imagen</title>
    <!-- Enlace CDN de Bootstrap 5 para que los estilos funcionen -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-dark">Subir Nueva Imagen</h3>
                            <p class="text-muted small">Completa los datos para guardar tu archivo</p>
                        </div>

                        <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Campo Nombre -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Nombre del archivo</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Ej. Imagen de perfil" required>
                            </div>

                            <!-- Campo Archivo -->
                            <div class="mb-3">
                                <label for="file-input" class="form-label fw-semibold">Seleccionar imagen</label>
                                <input type="file" class="form-control" id="file-input" name="file" accept="image/*" required>
                            </div>

                            <!-- Vista previa de la imagen -->
                            <div class="mb-4 text-center">
                                <img id="preview" src="#" alt="Vista previa" class="img-fluid rounded border shadow-sm" style="max-height: 180px; display: none;">
                            </div>

                            <!-- Botón de Envío -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary py-2 fw-semibold">Subir archivo</button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Script de JavaScript para la vista previa -->
    <script>
        document.getElementById('file-input').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('preview');
                    preview.src = e.target.result;
                    preview.style.display = 'inline-block';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>