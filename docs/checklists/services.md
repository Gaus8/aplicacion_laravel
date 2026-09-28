# Checklist del módulo 2 — Servicios

Estado: implementación lista para revisión; no cerrado.

## Datos y relaciones

- [x] Migración con título, slug único, resumen, descripción, llamada a la acción, orden y estado.
- [x] Relaciones opcionales de creación y actualización con usuarios.
- [x] Modelo con cast de estado y posición.

## Administración y autorización

- [x] Listado paginado con búsqueda y filtro por estado.
- [x] Alta, edición y eliminación con confirmación.
- [x] Slug generado de forma única a partir del título.
- [x] Form Requests separados para listado, alta y edición.
- [x] Policy para listar, crear, editar y eliminar.

## Publicación y seguridad

- [x] Solo servicios activos se muestran en el inicio público.
- [x] Enlaces opcionales deben usar HTTP o HTTPS y completarse en pareja con su etiqueta.
- [x] Vistas usan componentes Blade existentes y escapan el contenido editorial.
- [x] Pruebas feature para autorización, CRUD, slug, validación y presentación pública.
- [ ] Revisión funcional del usuario.
