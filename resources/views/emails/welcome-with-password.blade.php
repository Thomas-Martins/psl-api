<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('emails.welcome.subject', ['app_name' => $app_name]) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #297CE7;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 12px;
        }
        .logo {
            max-width: 200px;
            height: auto;
            filter: brightness(0) invert(1);
        }
        .content {
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 12px;
        }
        .footer {
            margin-top: 30px;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .credentials {
            background-color: #f8f9fa;
            padding: 15px 30px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/logos/PslSolutions.svg') }}" alt="{{ $app_name }}" class="logo">
    </div>

    <div class="content">
        <h2>{{ __('emails.welcome.title', ['firstname' => $user->firstname]) }}</h2>

        <p>{{ __('emails.welcome.account_created') }}</p>

        <div class="credentials">
            <p><strong>{{ __('emails.welcome.email') }} :</strong> {{ $user->email }}</p>
            <p><strong>{{ __('emails.welcome.temp_password') }} :</strong> {{ $password }}</p>
        </div>

        <p><strong>{{ __('emails.welcome.security_notice') }}</strong></p>

        <p>{{ __('emails.welcome.unauthorized_notice') }}</p>

        <p>{{ __('emails.welcome.regards') }}<br>
        {{ __('emails.welcome.team', ['app_name' => $app_name]) }}</p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} {{ $app_name }}. {{ __('emails.welcome.rights_reserved') }}</p>
    </div>
</body>
</html> 
