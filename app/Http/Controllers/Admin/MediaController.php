<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Media; // Asegúrate de importar tu modelo si vas a guardar en base de datos

class MediaController extends Controller
{
    /**
     * Muestra el formulario de carga.
     */
    public function create()
    {
        return view('admin.subirImagen'); // Asegúrate de que coincida con el nombre real de tu archivo Blade
    }

    /**
     * Procesa, valida y guarda el archivo.
     */
    public function store(Request $request)
    {
        // 1. La validación va AQUÍ adentro, dentro de la función store
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'file' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:5120'
            ],
        ]);

        // 2. Procesamos el archivo
        $file = $request->file('file');
        $path = $file->store('media', 'public');

        // 3. Guardamos en la base de datos usando el modelo
        Media::create([
            'name' => $validated['name'],
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        // 4. Redireccionamos con un mensaje de éxito
        return redirect()
            ->back() // O puedes usar ->route('admin.media.subirImgen') si prefieres
            ->with('success', 'Archivo cargado correctamente.');
    }
}