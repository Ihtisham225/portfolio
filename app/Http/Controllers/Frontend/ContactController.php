<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\Setting;
use App\Services\SeoService;
use App\Mail\ContactAutoReply;
use App\Mail\ContactMessage;
use App\Mail\NewsletterWelcome;
use App\Mail\NewsletterVerification;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    public function index()
    {
        // Get user info
        $user = User::first();
        
        // Get contact settings
        $contactSettings = Cache::remember('contact_settings', 3600, function () {
            return Setting::whereIn('key', [
                'email',
                'phone',
                'address',
                'facebook',
                'twitter',
                'linkedin',
                'github',
                'instagram',
                'business_hours',
                'timezone',
                'response_time',
            ])->pluck('value', 'key')->toArray();
        });

        // Format social links
        $socialLinks = [
            'github' => $contactSettings['github'] ?? '#',
            'linkedin' => $contactSettings['linkedin'] ?? '#',
            'twitter' => $contactSettings['twitter'] ?? '#',
            'instagram' => $contactSettings['instagram'] ?? '#',
            'facebook' => $contactSettings['facebook'] ?? '#',
        ];

        // Parse business hours if provided
        $businessHours = $this->parseBusinessHours($contactSettings['business_hours'] ?? null);
        
        // Format contact info
        $contactInfo = [
            'email' => $contactSettings['email'] ?? 'hello@example.com',
            'phone' => $contactSettings['phone'] ?? '+1 (555) 123-4567',
            'address' => $contactSettings['address'] ?? 'San Francisco, CA',
            'timezone' => $contactSettings['timezone'] ?? 'EST',
            'response_time' => $contactSettings['response_time'] ?? '24-48 hours',
            'business_hours_raw' => $contactSettings['business_hours'] ?? 'Monday - Friday: 9:00 AM - 6:00 PM',
            'business_hours_parsed' => $businessHours,
        ];
        
        // Generate SEO meta tags
        $metaTags = $this->seoService->generateMetaTags([
            'title' => 'Contact - ' . config('app.name'),
            'description' => 'Get in touch for project inquiries, collaborations, or any questions you may have.',
            'keywords' => 'contact, get in touch, email, phone, inquiry, message',
        ]);

        // Generate breadcrumbs
        $breadcrumbs = $this->seoService->generateBreadcrumbs([
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Contact', 'url' => null]
        ]);

        return view('frontend.pages.contact', compact(
            'user',
            'socialLinks',
            'contactInfo',
            'metaTags',
            'breadcrumbs'
        ));
    }

    /**
     * Parse business hours string into structured array
     */
    private function parseBusinessHours(?string $hoursString): array
    {
        if (!$hoursString) {
            return [
                ['days' => 'Monday - Friday', 'hours' => '9:00 AM - 6:00 PM'],
                ['days' => 'Saturday', 'hours' => '10:00 AM - 4:00 PM'],
                ['days' => 'Sunday', 'hours' => 'Closed'],
            ];
        }

        // Simple parsing - adjust based on your format
        $lines = explode("\n", $hoursString);
        $parsed = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, ':') !== false) {
                $parts = explode(':', $line, 2);
                $parsed[] = [
                    'days' => trim($parts[0]),
                    'hours' => trim($parts[1]),
                ];
            }
        }
        
        return !empty($parsed) ? $parsed : [
            ['days' => 'Monday - Friday', 'hours' => '9:00 AM - 6:00 PM'],
            ['days' => 'Saturday', 'hours' => '10:00 AM - 4:00 PM'],
            ['days' => 'Sunday', 'hours' => 'Closed'],
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:5000',
            'g-recaptcha-response' => 'required|captcha',
        ]);

        // Create message
        $message = Message::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'user_agent' => [
                'browser' => $this->getBrowser($request->header('User-Agent')),
                'platform' => $this->getPlatform($request->header('User-Agent')),
                'device' => $this->getDevice($request->header('User-Agent')),
            ],
        ]);

        // Get admin email from settings
        $adminEmail = Setting::getValue('contact_email', config('mail.from.address'));
        
        // Send notification to admin
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new ContactMessage($message));
            } catch (\Exception $e) {
                Log::error('Failed to send contact email to admin: ' . $e->getMessage());
            }
        }

        // Send auto-reply to sender
        $autoReplyEnabled = Setting::getValue('contact_auto_reply', true);
        if ($autoReplyEnabled) {
            try {
                Mail::to($validated['email'])->send(new ContactAutoReply($message));
            } catch (\Exception $e) {
                Log::error('Failed to send auto-reply email: ' . $e->getMessage());
            }
        }

        // Clear cache for dashboard stats
        Cache::forget('recent_messages');
        Cache::forget('unread_messages_count');

        return redirect()->back()->with('success', 'Your message has been sent successfully! I\'ll get back to you soon.');
    }

    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'name'  => 'nullable|string|max:255',
        ]);

        // Check if already subscribed
        $existing = NewsletterSubscription::where('email', $validated['email'])->first();

        if ($existing) {
            if (!$existing->is_active) {
                // Resubscribe
                $existing->resubscribe();
                $existing->name = $validated['name']
                    ?? $existing->name
                    ?? $this->nameFromEmail($validated['email']);
                $existing->save();
                
                // Send welcome back email
                Mail::to($validated['email'])->send(new NewsletterWelcome($existing, true));
                
                return response()->json([
                    'success' => true,
                    'message' => 'Welcome back! You have been resubscribed to our newsletter.',
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'You are already subscribed to our newsletter.',
            ], 422);
        }

        // Create new subscription
        $subscription = NewsletterSubscription::create([
            'email' => $validated['email'],
            'name' => $validated['name']
                ?? $this->nameFromEmail($validated['email']),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'verification_token' => \Illuminate\Support\Str::random(40),
        ]);

        // Send verification email
        Mail::to($validated['email'])->send(new NewsletterVerification($subscription));

        // Send notification to admin if enabled
        $notifyAdmin = Setting::getValue('newsletter_notify_admin', false);
        if ($notifyAdmin) {
            $adminEmail = Setting::getValue('contact_email', config('mail.from.address'));
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new \App\Mail\NewsletterNewSubscriber($subscription));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank you for subscribing! Please check your email to verify your subscription.',
        ]);
    }

    private function nameFromEmail(string $email): string
    {
        $localPart = explode('@', $email)[0];

        // Replace dots, underscores, hyphens with spaces
        $name = preg_replace('/[._-]+/', ' ', $localPart);

        // Remove numbers
        $name = preg_replace('/\d+/', '', $name);

        // Clean & format
        $name = trim($name);

        return $name ? ucwords($name) : 'Subscriber';
    }

    public function verifySubscription(Request $request, $token)
    {
        $subscription = NewsletterSubscription::where('verification_token', $token)->first();

        if (!$subscription) {
            return redirect()->route('home')->with('error', 'Invalid verification token.');
        }

        if ($subscription->is_verified) {
            return redirect()->route('home')->with('info', 'Your email is already verified.');
        }

        $subscription->verify();
        
        // Send welcome email
        Mail::to($subscription->email)->send(new NewsletterWelcome($subscription));

        return redirect()->route('home')->with('success', 'Thank you for verifying your email! You are now subscribed to our newsletter.');
    }

    public function unsubscribe(Request $request, $email)
    {
        $subscription = NewsletterSubscription::where('email', $email)->first();

        if (!$subscription) {
            return redirect()->route('home')->with('error', 'Subscription not found.');
        }

        if (!$subscription->is_active) {
            return redirect()->route('home')->with('info', 'You are already unsubscribed.');
        }

        $subscription->unsubscribe();

        return redirect()->route('home')->with('info', 'You have been unsubscribed from our newsletter.');
    }

    /**
     * Helper methods for user agent detection
     */
    private function getBrowser($userAgent)
    {
        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'Edge';
        } elseif (strpos($userAgent, 'Opera') !== false) {
            return 'Opera';
        }
        return 'Unknown';
    }

    private function getPlatform($userAgent)
    {
        $platforms = [
            'Windows' => 'Windows',
            'Macintosh' => 'Mac',
            'Linux' => 'Linux',
            'Android' => 'Android',
            'iPhone' => 'iOS',
            'iPad' => 'iOS',
        ];

        foreach ($platforms as $key => $value) {
            if (stripos($userAgent, $key) !== false) {
                return $value;
            }
        }

        return 'Unknown';
    }

    private function getDevice($userAgent)
    {
        if (stripos($userAgent, 'mobile') !== false) {
            return 'Mobile';
        } elseif (stripos($userAgent, 'tablet') !== false) {
            return 'Tablet';
        }
        return 'Desktop';
    }
}