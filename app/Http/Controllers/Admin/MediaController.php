<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexMediaRequest;
use App\Http\Requests\Admin\StoreMediaRequest;
use Illuminate\Http\Request;
use App\Models\Media; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Mail\ContactMessage;
use App\Services\SmtpMailer;
use App\Services\AuditLogger;
use Throwable;

class MediaController extends Controller
{
    public function index(IndexMediaRequest $request)
    {
        Gate::authorize('viewAny', Media::class);

        $search = trim((string) $request->validated('search', ''));
        $media = Media::query()
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%' . $search . '%'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.media.index', compact('media', 'search'));
    }

    /**
     * Muestra el formulario de carga.
     */
    public function create()
    {
        Gate::authorize('create', Media::class);

        return view('admin.subirImagen'); // Asegúrate de que coincida con el nombre real de tu archivo Blade
    }

    /**
     * Procesa, valida y guarda el archivo.
     */
    public function store(StoreMediaRequest $request, AuditLogger $auditLogger)
    {
        $validated = $request->validated();
        $file = $request->file('file');
        $path = $file->store('media', 'public');

        try {
            $media = Media::create([
                'name' => $validated['name'],
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($path);
            throw $exception;
        }

        $auditLogger->record('media.uploaded', 'success', $request->user(), 'Elemento multimedia', [
            'media_id' => $media->getKey(),
        ]);

        return redirect()->route('admin.media.index')->with('success', 'Imagen cargada correctamente.');
    }

    public function destroy(Media $media, AuditLogger $auditLogger)
    {
        Gate::authorize('delete', $media);

        $path = $media->path;
        $mediaId = $media->getKey();
        DB::transaction(fn () => $media->delete());
        Storage::disk('public')->delete($path);
        $auditLogger->record('media.deleted', 'success', request()->user(), 'Elemento multimedia', [
            'media_id' => $mediaId,
        ]);

        return redirect()->route('admin.media.index')->with('success', 'Imagen eliminada correctamente.');
    }

    public function file(Media $media)
    {
        Gate::authorize('view', $media);

        return Storage::disk('public')->response($media->path, null, [
            'Content-Type' => $media->mime_type ?: 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
        ], 'inline');
    }

    /**
     * Muestra la vista de envío de correos.
     */
    public function emails()
    {
        return view('admin.emails');
    }

    /**
     * Procesa el envío del correo desde el formulario.
     */
    public function sendEmail(Request $request, SmtpMailer $smtpMailer, AuditLogger $auditLogger)
    {
        $validated = $request->validate([
            'recipient' => 'required|email',
            'subject'   => 'required|string|max:255',
            'message'   => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:10240',
        ]);

        $data = [
            'name'    => auth()->user()->name,
            'email'   => auth()->user()->email,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ];

        $files = $request->file('attachments') ?? [];

        // 1. Enviar el email
        $smtpMailer->mailer()->to($validated['recipient'])->send(new ContactMessage($data, $files));

        // 2. REGISTRAR EN LA BASE DE DATOS
        $sentEmail = \App\Models\EmailSent::create([
            'user_id'   => auth()->id(),
            'recipient' => $validated['recipient'],
            'subject'   => $validated['subject'],
            'message'   => $validated['message'],
        ]);
        $auditLogger->record('email.sent', 'success', $request->user(), 'Correo enviado', [
            'email_sent_id' => $sentEmail->getKey(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Correo electrónico enviado y registrado correctamente.');
    }
}
