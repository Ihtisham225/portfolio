<?php

namespace App\Mail;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactAutoReply extends Mailable
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
        $siteName = $this->settings['site_name'] ?? config('app.name');
        
        return $this->subject('Thank you for contacting ' . $siteName)
                    ->markdown('emails.contact.auto-reply')
                    ->with([
                        'message' => $this->message,
                        'settings' => $this->settings,
                    ]);
    }
}