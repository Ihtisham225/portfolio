@component('mail::message')
# Thank You for Contacting Us!

Hi {{ $message->name }},

Thank you for reaching out to us. We have received your message and will get back to you as soon as possible.

## Your Message Summary
**Subject:** {{ $message->subject }}  
**Submitted:** {{ $message->created_at->format('F j, Y \a\t g:i A') }}  

We typically respond within 24-48 hours during business days.

@component('mail::panel')
If you have any urgent inquiries, please feel free to call us at:
**Phone:** {{ $settings['contact_phone'] ?? 'Not specified' }}
@endcomponent

## What to Expect Next
1. We'll review your message
2. Our team will assess your requirements
3. We'll contact you with a detailed response

@component('mail::button', ['url' => route('portfolio'), 'color' => 'success'])
View Our Portfolio
@endcomponent

Best regards,  
**{{ $settings['site_name'] ?? config('app.name') }} Team**

---

*This is an automated response. Please do not reply to this email.*
@endcomponent