@component('mail::message')
# Confirm Your Email Address

Hi {{ $subscription->name ?? 'there' }},

Thank you for subscribing to the **{{ $settings['site_name'] ?? config('app.name') }}** newsletter!

Please confirm your email address by clicking the button below:

@component('mail::button', ['url' => route('newsletter.verify', ['token' => $subscription->verification_token]), 'color' => 'primary'])
Verify Email Address
@endcomponent

Or copy and paste this URL into your browser:  
{{ route('newsletter.verify', ['token' => $subscription->verification_token]) }}

## Why Verify?
Verifying your email ensures:
- ✅ You'll receive all our updates
- ✅ We can deliver emails successfully
- ✅ You have control over your subscription

This link will expire in 24 hours.

## Didn't Request This?
If you didn't subscribe to our newsletter, please ignore this email. Your email address will not be added to our list.

@component('mail::panel')
**Subscription Details:**  
**Email:** {{ $subscription->email }}  
**Requested:** {{ $subscription->created_at->format('F j, Y \a\t g:i A') }}
@endcomponent

Best regards,  
**{{ $settings['site_name'] ?? config('app.name') }} Team**

---

*This is an automated verification email. Please do not reply to this message.*
@endcomponent