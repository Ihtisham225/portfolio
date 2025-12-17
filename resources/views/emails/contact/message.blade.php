@component('mail::message')
# New Contact Form Submission

A new message has been submitted through the contact form.

## Message Details

**Name:** {{ $message->name }}  
**Email:** {{ $message->email }}  
**Subject:** {{ $message->subject }}  
**Submitted:** {{ $message->created_at->format('F j, Y \a\t g:i A') }}  

## Message Content
@component('mail::panel')
{{ $message->message }}
@endcomponent

## Technical Details
- **IP Address:** {{ $message->ip_address }}
- **Browser:** {{ $message->browser }}
- **Platform:** {{ $message->platform }}
- **Device:** {{ $message->device ?? 'Unknown' }}

@component('mail::button', ['url' => route('admin.messages.show', $message->id), 'color' => 'primary'])
View in Dashboard
@endcomponent

@if($message->status === 'unread')
This message is currently marked as **unread**.
@endif

Thanks,<br>
{{ $settings['site_name'] ?? config('app.name') }}
@endcomponent