<?php

namespace App\Mail;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $settings;

    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->settings = \App\Models\Setting::getAll();
    }

    public function build()
    {
        return $this->subject('New Contact Message: ' . $this->message->subject)
                    ->markdown('emails.contact.message')
                    ->with([
                        'message' => $this->message,
                        'settings' => $this->settings,
                    ]);
    }
}