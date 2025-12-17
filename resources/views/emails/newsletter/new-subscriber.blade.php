@component('mail::message')
# New Newsletter Subscriber

A new user has subscribed to your newsletter.

## Subscriber Details
**Name:** {{ $subscription->name ?? 'Not provided' }}  
**Email:** {{ $subscription->email }}  
**Subscribed:** {{ $subscription->created_at->format('F j, Y \a\t g:i A') }}  
**Status:** {{ $subscription->status }}  

## Technical Information
- **IP Address:** {{ $subscription->ip_address }}
- **User Agent:** {{ $subscription->user_agent }}
- **Requires Verification:** {{ $subscription->is_verified ? 'No' : 'Yes' }}

## Total Subscribers
Current active subscribers: {{ \App\Models\NewsletterSubscription::verified()->active()->count() }}

@component('mail::button', ['url' => route('admin.newsletter.index'), 'color' => 'primary'])
View All Subscribers
@endcomponent

@component('mail::button', ['url' => route('admin.dashboard'), 'color' => 'success'])
Go to Dashboard
@endcomponent

Thanks,<br>
{{ $settings['site_name'] ?? config('app.name') }} System
@endcomponent