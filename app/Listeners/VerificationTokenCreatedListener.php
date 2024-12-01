<?php

namespace App\Listeners;

use App\Events\VerificationTokenCreatedEvent;
use App\Notifications\PasswordlessLinkNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class VerificationTokenCreatedListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(VerificationTokenCreatedEvent $event): void
    {
        Notification::route('mail', $event->token->getEmail())
            ->notify(new PasswordlessLinkNotification($event->token->generateLink()));
    }
}