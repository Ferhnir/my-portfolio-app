<?php

namespace App\Services;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactMessageService
{
    public function issueToken(): string
    {
        return Crypt::encryptString((string) now()->getTimestamp());
    }

    public function issuedAt(string $token): ?CarbonInterface
    {
        try {
            $stamp = Crypt::decryptString($token);
        } catch (DecryptException) {
            return null;
        }

        if (! ctype_digit($stamp)) {
            return null;
        }

        return Carbon::createFromTimestamp((int) $stamp);
    }

    /**
     * Store a real message. A filled honeypot or a form that was just opened
     * returns false and leaves no row, so the caller can answer either way
     * with the same success response.
     *
     * @param  array{name: string, email: string, message: string, form_token: string, company_website?: string|null}  $attributes
     *
     * @throws Throwable
     */
    public function accept(array $attributes): bool
    {
        if (trim((string) ($attributes['company_website'] ?? '')) !== '') {
            return false;
        }

        $issued = $this->issuedAt($attributes['form_token']);
        $minSeconds = (int) config('contact.min_seconds');

        if ($issued === null || $issued->greaterThan(now()->subSeconds($minSeconds))) {
            return false;
        }

        $message = DB::transaction(fn (): ContactMessage => ContactMessage::query()->create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'message' => $attributes['message'],
        ]));

        Mail::to((string) config('contact.inbox'))->send(new ContactMessageReceived($message));

        return true;
    }
}
