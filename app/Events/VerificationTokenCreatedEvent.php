<?php

namespace App\Events;

use App\Models\VerificationToken;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VerificationTokenCreatedEvent
{
    use Dispatchable, SerializesModels;

    public VerificationToken $token;

    /**
     * Create a new event instance.
     */
    public function __construct(VerificationToken $token)
    {
        $this->token = $token;
    }
}