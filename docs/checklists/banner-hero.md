# Checklist del módulo 1 — Banners / Hero

Estado: implementación lista para revisión; no cerrado.

## Datos y relaciones

- [x] Migración `banners` con contenido, imagen, estado, posición y fechas de publicación.
- [x] Relaciones opcionales de creación y actualización con usuarios.
- [x] Modelo con casts y consulta de banners publicables.

## Administración y autorización

- [x] Listado, búsqueda, filtro de estado, paginación y orden.
- [x] Formulario de creación y edición con vista previa de imagen.
- [x] Eliminación del registro y su imagen almacenada.
- [x] Form Requests separados para listado, creación y actualización.
- [x] Policy para lectura, creación, actualización y eliminación.

## Publicación

- [x] Hero estático cuando hay un banner publicable.
- [x] Carrusel accesible con controles manuales cuando hay varios banners.
- [x] Filtro por estado y ventana de publicación.
- [x] Botón opcional validado para HTTP/HTTPS.

## Seguridad y verificación

- [x] Validación de imagen por contenido/tipo, formatos JPG/PNG/WebP y máximo 5 MB.
- [x] Archivos gestionados mediante Laravel Storage en el disco `public`.
- [x] Pruebas feature de acceso, CRUD, validación, fechas y presentación pública.
- [ ] Revisión funcional del usuario.
