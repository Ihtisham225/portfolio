<?php

namespace App\Mail;

use App\Models\NewsletterSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;
    public $settings;
    public $isResubscribe;

    public function __construct(NewsletterSubscription $subscription, $isResubscribe = false)
    {
        $this->subscription = $subscription;
        $this->settings = \App\Models\Setting::getAll();
        $this->isResubscribe = $isResubscribe;
    }

    public function build()
    {
        $siteName = $this->settings['site_name'] ?? config('app.name');
        $subject = $this->isResubscribe 
            ? 'Welcome Back to ' . $siteName . ' Newsletter'
            : 'Welcome to ' . $siteName . ' Newsletter';

        return $this->subject($subject)
                    ->markdown('emails.newsletter.welcome')
                    ->with([
                        'subscription' => $this->subscription,
                        'settings' => $this->settings,
                        'isResubscribe' => $this->isResubscribe,
                    ]);
    }
}