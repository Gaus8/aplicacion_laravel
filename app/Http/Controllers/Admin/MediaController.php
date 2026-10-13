<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexMediaRequest;
use App\Http\Requests\Admin\StoreMediaRequest;
use Illuminate\Http\Request;
use App\Models\Media;
use App\Models\EmailSent;
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
        $mediaQuery = Media::query()
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%' . $search . '%'));

        if ($this->wantsJsonResponse($request)) {
            $items = $mediaQuery->latest()->get();

            return response()->json([
                'data' => $items->map(fn (Media $item) => [
                    'id' => $item->getKey(),
                    'name' => $item->name,
                    'mime_type' => $item->mime_type,
                    'size' => $item->size,
                    'url' => route('admin.media.file', $item),
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ]),
                'count' => $items->count(),
            ]);
        }

        $media = $mediaQuery->latest()->paginate(12)->withQueryString();

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

        if ($this->wantsJsonResponse($request)) {
            $fileUrl = route('admin.media.file', $media);

            return response()->json([
                'message' => 'Imagen cargada correctamente.',
                'data' => [
                    'id' => $media->getKey(),
                    'name' => $media->name,
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                    'url' => $fileUrl,
                ],
            ], 201, ['Location' => $fileUrl]);
        }

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
    public function emails(Request $request)
    {
        if ($this->wantsJsonResponse($request)) {
            $emails = EmailSent::query()->latest()->get();

            return response()->json([
                'data' => $emails->map(fn (EmailSent $email) => [
                    'id' => $email->getKey(),
                    'user_id' => $email->user_id,
                    'recipient' => $email->recipient,
                    'subject' => $email->subject,
                    'message' => $email->message,
                    'created_at' => $email->created_at,
                    'updated_at' => $email->updated_at,
                ]),
                'count' => $emails->count(),
            ]);
        }

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

        if ($this->wantsJsonResponse($request)) {
            return response()->json([
                'message' => 'Correo electrónico enviado y registrado correctamente.',
                'data' => [
                    'id' => $sentEmail->getKey(),
                    'user_id' => $sentEmail->user_id,
                    'recipient' => $sentEmail->recipient,
                    'subject' => $sentEmail->subject,
                    'message' => $sentEmail->message,
                    'created_at' => $sentEmail->created_at,
                ],
            ], 200);
        }

        return redirect()->route('dashboard')->with('success', 'Correo electrónico enviado y registrado correctamente.');
    }

    private function wantsJsonResponse(Request $request): bool
    {
        $accept = strtolower((string) $request->header('Accept', ''));

        return $request->wantsJson() || str_contains($accept, 'application/json');
    }
}
