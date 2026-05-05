<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to CvSU Accreditation Archives</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f4f7f6;
            padding-bottom: 40px;
        }
        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-spacing: 0;
            color: #2d3748;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: linear-gradient(135deg, #1a4d2e 0%, #2d6a4f 100%);
            padding: 40px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .content {
            padding: 40px;
            line-height: 1.6;
        }
        .content h2 {
            margin-top: 0;
            font-size: 20px;
            color: #1a202c;
        }
        .credentials {
            background-color: #f7fafc;
            border: 1px solid #edf2f7;
            border-radius: 6px;
            padding: 24px;
            margin: 24px 0;
        }
        .credential-item {
            margin-bottom: 12px;
        }
        .credential-item:last-child {
            margin-bottom: 0;
        }
        .label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #718096;
            display: block;
            margin-bottom: 4px;
        }
        .value {
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 16px;
            color: #2d3748;
            background: #ffffff;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            display: block;
        }
        .button-wrapper {
            text-align: center;
            margin-top: 32px;
        }
        .button {
            background-color: #1a4d2e;
            color: #ffffff;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            display: inline-block;
            transition: background-color 0.2s;
        }
        .footer {
            text-align: center;
            padding: 24px;
            font-size: 12px;
            color: #a0aec0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main">
            <tr>
                <td class="header">
                    <h1>CvSU Accreditation Archives</h1>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <h2>Hello, {{ $user->name }}!</h2>
                    <p>Your account has been successfully created in the CvSU Accreditation Archives System. You can now log in using the credentials provided below.</p>
                    
                    <div class="credentials">
                        <div class="credential-item">
                            <span class="label">Email Address</span>
                            <span class="value">{{ $user->email }}</span>
                        </div>
                        <div class="credential-item">
                            <span class="label">Temporary Password</span>
                            <span class="value">{{ $password }}</span>
                        </div>
                    </div>

                    <p>For security reasons, we recommend that you change your password after your first login.</p>

                    <div class="button-wrapper">
                        <a href="{{ url('/auth') }}" class="button">Log In to Your Account</a>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    &copy; {{ date('Y') }} Cavite State University. All rights reserved.<br>
                    This is an automated message, please do not reply.
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
