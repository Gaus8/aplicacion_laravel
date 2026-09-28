<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactSubmissionRequest;
use App\Mail\ContactSubmissionNotification;
use App\Models\ContactSubmission;
use App\Services\SmtpMailer;
use Illuminate\Support\Facades\Log;
use Throwable;

class ContactController extends Controller
{
    public function create()
    {
        return view('public.contact');
    }

    public function store(StoreContactSubmissionRequest $request, SmtpMailer $smtpMailer)
    {
        $data = $request->validated();
        if (filled($data['website'] ?? null)) {
            return redirect()->route('contact.create')->with('success', 'Gracias. Tu mensaje fue recibido.');
        }

        $submission = ContactSubmission::create([
            'name' => trim($data['name']),
            'email' => trim($data['email']),
            'subject' => trim($data['subject']),
            'message' => trim($data['message']),
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 512),
        ]);

        $recipient = config('mail.from.address');
        $notificationSent = false;
        if (filled($recipient)) {
            try {
                $smtpMailer->mailer()->to($recipient)->send(new ContactSubmissionNotification($submission));
                $notificationSent = true;
            } catch (Throwable $exception) {
                Log::warning('Contact notification could not be sent.', ['contact_submission_id' => $submission->getKey()]);
            }
        }

        return redirect()->route('contact.create')->with('success', 'Gracias. Tu mensaje fue recibido.')
            ->with('notificationPending', !$notificationSent);
    }
}
