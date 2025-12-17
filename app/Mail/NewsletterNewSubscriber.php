<?php

namespace App\Mail;

use App\Models\NewsletterSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterNewSubscriber extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;
    public $settings;

    public function __construct(NewsletterSubscription $subscription)
    {
        $this->subscription = $subscription;
        $this->settings = \App\Models\Setting::getAll();
    }

    public function build()
    {
        $siteName = $this->settings['site_name'] ?? config('app.name');
        
        return $this->subject('New Newsletter Subscriber - ' . $siteName)
                    ->markdown('emails.newsletter.new-subscriber')
                    ->with([
                        'subscription' => $this->subscription,
                        'settings' => $this->settings,
                    ]);
    }
}