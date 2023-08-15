<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use JetBrains\PhpStorm\Pure;

class SendPayoutEmail extends Mailable {

    /**
     * Create a new message instance.
     */
    public function __construct(
        public $amount,
    ) {}

    /**
     * Get the message content definition.
     */
    #[Pure] public function content(): Content
    {
        return new Content(
            text: "Here is your Payout: USD $this->amount",
        );
    }
}
