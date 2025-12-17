<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? '' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #374151;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .email-header {
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            color: white;
            padding: 32px 24px;
            text-align: center;
        }
        
        .email-body {
            padding: 32px 24px;
        }
        
        .email-footer {
            background-color: #f3f4f6;
            padding: 24px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 8px 4px;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        }
        
        .panel {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin: 16px 0;
        }
        
        @media (max-width: 640px) {
            .email-container {
                border-radius: 0;
            }
            
            .email-body {
                padding: 24px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1 style="margin: 0; font-size: 24px; font-weight: 700;">
                {{ $settings['site_name'] ?? config('app.name') }}
            </h1>
            <p style="margin: 8px 0 0; opacity: 0.9; font-size: 14px;">
                {{ $settings['site_description'] ?? 'Professional Portfolio' }}
            </p>
        </div>
        
        <div class="email-body">
            {{ $slot }}
        </div>
        
        <div class="email-footer">
            <p style="margin: 0 0 8px;">
                &copy; {{ date('Y') }} {{ $settings['site_name'] ?? config('app.name') }}. All rights reserved.
            </p>
            <p style="margin: 0; font-size: 12px; color: #9ca3af;">
                {{ $settings['contact_email'] ?? '' }}
                @if($settings['contact_email'])
                    <br>
                @endif
                <a href="{{ route('newsletter.unsubscribe', ['email' => $subscription->email ?? '']) }}" 
                   style="color: #6b7280; text-decoration: underline;">
                    Unsubscribe
                </a>
            </p>
        </div>
    </div>
</body>
</html>