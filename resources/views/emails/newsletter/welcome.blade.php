@component('mail::message')
@if($isResubscribe)
# Welcome Back!
@else
# Welcome to Our Community! 🎉
@endif

@if($isResubscribe)
We're thrilled to have you back, {{ $subscription->name ?? 'there' }}!
@else
Thank you for subscribing to the **{{ $settings['site_name'] ?? config('app.name') }}** newsletter, {{ $subscription->name ?? 'there' }}!
@endif

We're excited to share the latest updates, projects, and insights with you.

## What You'll Receive
- 📢 Latest project updates and launches
- 💡 Web development tips and tutorials
- 🚀 Technology insights and trends
- 🎯 Exclusive content and resources

@component('mail::panel')
**Subscription Details:**  
**Email:** {{ $subscription->email }}  
**Status:** Active  
**Joined:** {{ $subscription->created_at->format('F j, Y') }}
@endcomponent

## Get Started
@component('mail::button', ['url' => route('portfolio'), 'color' => 'primary'])
Explore Our Portfolio
@endcomponent

@component('mail::button', ['url' => route('blog'), 'color' => 'success'])
Read Our Blog
@endcomponent

## Stay Connected
Follow us on social media for more updates:

@if($settings['social_github'] ?? false)
- **GitHub:** [{{ $settings['social_github'] }}]({{ $settings['social_github'] }})
@endif
@if($settings['social_linkedin'] ?? false)
- **LinkedIn:** [{{ $settings['social_linkedin'] }}]({{ $settings['social_linkedin'] }})
@endif
@if($settings['social_twitter'] ?? false)
- **Twitter:** [{{ $settings['social_twitter'] }}]({{ $settings['social_twitter'] }})
@endif

## Manage Your Subscription
You can unsubscribe at any time by clicking the link in our emails or visiting:
@component('mail::button', ['url' => route('newsletter.unsubscribe', ['email' => $subscription->email]), 'color' => 'warning'])
Unsubscribe
@endcomponent

Looking forward to sharing great content with you!

Best regards,  
**{{ $settings['site_name'] ?? config('app.name') }}**

---

*If you did not sign up for this newsletter, please ignore this email or contact us.*
@endcomponent