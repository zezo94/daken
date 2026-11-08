<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Services\ContactEmailNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('pages.contact');
    }

    public function store(
        StoreContactMessageRequest $request,
        ContactEmailNotifier $notifier
    ): RedirectResponse {

        // Save the customer's message first.
        $message = ContactMessage::create(
            $request->validated()
        );

        // Queue notification emails without waiting for SMTP.
        try {
            $notifier->dispatch($message);
        } catch (Throwable $exception) {
            Log::error('DAKEN contact email queue dispatch failed', [
                'message_id' => $message->getKey(),
                'exception' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('contact.index')
            ->with('success', __('contact.success'));
    }
}
