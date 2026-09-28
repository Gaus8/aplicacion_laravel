<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexContactSubmissionRequest;
use App\Http\Requests\Admin\MarkContactReadRequest;
use App\Models\ContactSubmission;
use Illuminate\Support\Facades\Gate;

class ContactSubmissionController extends Controller
{
    public function index(IndexContactSubmissionRequest $request)
    {
        $filters = $request->validated();
        $messages = ContactSubmission::query()
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $term = '%'.trim($search).'%';
                $query->where(fn ($query) => $query->where('name', 'like', $term)->orWhere('email', 'like', $term)->orWhere('subject', 'like', $term));
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $status === 'unread' ? $query->whereNull('read_at') : $query->whereNotNull('read_at'))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.contacts.index', compact('messages', 'filters'));
    }

    public function markRead(MarkContactReadRequest $request, ContactSubmission $submission)
    {
        Gate::authorize('update', $submission);
        $submission->read_at ??= now();
        $submission->save();

        return back()->with('success', 'El mensaje quedó marcado como leído.');
    }
}
