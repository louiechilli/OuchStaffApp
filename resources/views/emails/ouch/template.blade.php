<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Ouch Tattoo Studio' }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Georgia', 'Times New Roman', serif; background-color: #0d1410; color: #e8e5dd;">
    <style>
        /* General Styles */
        a {
            color: #c9a962 !important; 
            text-decoration: none !important; 
            word-break: break-all !important;
        }
        a:hover {
            text-decoration: none !important;
        }
    </style>
    
    <!-- Main Container -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #0d1410;">
        <tr>
            <td style="padding: 40px 20px;">
                
                <!-- Email Wrapper -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 650px; margin: 0 auto; background-color: #1a2820; border-radius: 2px; overflow: hidden; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6); border: 1px solid #2d4032;">
                    
                    <!-- Gold Top Border -->
                    <tr>
                        <td style="padding: 0;">
                            <div style="height: 3px; background: linear-gradient(90deg, #1a2820 0%, #c9a962 20%, #d4af6a 50%, #c9a962 80%, #1a2820 100%);"></div>
                        </td>
                    </tr>

                    <!-- Header with Image -->
                    @isset($headerImageUrl)
                    <tr>
                        <td style="padding: 0; background-color: #0f1913; background-image: url('{{ $headerImageUrl }}'); background-size: cover; background-position: center; height: 150px; border-bottom: 1px solid #2d4032;">
                            <div style="width: 100%; height: 100%; background-color: rgba(15, 25, 19, 0.6); display: flex; align-items: center; justify-content: center;">
                            </div>
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td style="background: linear-gradient(135deg, #1a2820 0%, #0f1913 100%); padding: 50px 40px; text-align: center; border-bottom: 1px solid #2d4032;">
                            <div style="width: 60px; height: 2px; background-color: #c9a962; margin: 20px auto 0; opacity: 0.6;"></div>
                        </td>
                    </tr>
                    @endisset

                    <!-- Ornamental Divider -->
                    <tr>
                        <td style="padding: 30px 40px 0 40px; text-align: center;">
                            <div style="color: #c9a962; font-size: 20px; opacity: 0.4; letter-spacing: 8px;">⟡ ⟡ ⟡</div>
                        </td>
                    </tr>

                    <!-- Main Content Area -->
                    <tr>
                        <td style="padding: 30px 50px 40px 50px;">
                            
                            <!-- Greeting -->
                            @isset($greeting)
                            <p style="margin: 0 0 30px 0; font-size: 20px; font-weight: 400; color: #c9a962; line-height: 1.4; font-style: italic; font-family: 'Georgia', serif;">
                                {{ $greeting }}
                            </p>
                            @endisset

                            <!-- First Line -->
                            @isset($first_line)
                            <div style="color: #d4d0c4; font-size: 16px; line-height: 1.9; font-family: 'Georgia', serif; margin-bottom: 35px;">
                                {!! nl2br(e($first_line)) !!}
                            </div>
                            @endisset

                            <!-- Html Content -->
                            @isset($html_content)
                            <div style="color: #d4d0c4; font-size: 16px; line-height: 1.9; font-family: 'Georgia', serif; margin-bottom: 35px;">
                                {!! $html_content !!}
                            </div>
                            @endisset
                        </td>
                    </tr>

                    <!-- Call to Action Button -->
                    @isset($actionUrl)
                    <tr>
                        <td style="padding: 0 50px 45px 50px; text-align: center;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 0 auto;">
                                <tr>
                                    <td style="background-color: #c9a962; border: 2px solid #d4af6a; position: relative;">
                                        <a href="{{ $actionUrl }}" style="display: inline-block; padding: 16px 45px; font-size: 14px; font-weight: 600; color: #0f1913; text-decoration: none; text-transform: uppercase; letter-spacing: 2px; font-family: 'Georgia', serif;">
                                            {{ $actionText ?? 'Continue' }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 20px 0 0 0; font-size: 13px; color: #7a8c7d; font-family: 'Georgia', serif; font-style: italic;">
                                Or copy this link: <a href="{{ $actionUrl }}" style="color: #c9a962; text-decoration: none; word-break: break-all;">{{ $actionUrl }}</a>
                            </p>
                        </td>
                    </tr>
                    @endisset

                    <!-- Ornamental Divider -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center;">
                            <div style="color: #c9a962; font-size: 20px; opacity: 0.4; letter-spacing: 8px;">⟡ ⟡ ⟡</div>
                        </td>
                    </tr>

                    <!-- Footer Content -->
                    @isset($footer)
                    <tr>
                        <td style="background-color: #151f18; padding: 35px 50px; border-top: 1px solid #2d4032;">
                            <p style="margin: 0; font-size: 14px; color: #9ba599; line-height: 1.8; text-align: center; font-family: 'Georgia', serif;">
                                {{ $footer }}
                            </p>
                        </td>
                    </tr>
                    @endisset

                    <!-- Social Links -->
                    @if(isset($socialLinks) && count($socialLinks) > 0)
                    <tr>
                        <td style="background-color: #0f1913; padding: 30px 40px; text-align: center; border-top: 1px solid #2d4032;">
                            <p style="margin: 0 0 20px 0; font-size: 12px; color: #c9a962; text-transform: uppercase; letter-spacing: 3px; font-weight: 600;">
                                Connect With Us
                            </p>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 0 auto;">
                                <tr>
                                    @foreach($socialLinks as $platform => $url)
                                    <td style="padding: 0 12px;">
                                        <a href="{{ $url }}" style="display: inline-block; width: 42px; height: 42px; background-color: #1a2820; border: 1px solid #2d4032; text-align: center; line-height: 42px; color: #c9a962; text-decoration: none; font-weight: 600; font-size: 11px; text-transform: uppercase; font-family: 'Georgia', serif; transition: all 0.3s;">
                                            {{ substr($platform, 0, 2) }}
                                        </a>
                                    </td>
                                    @endforeach
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    <!-- Legal Footer -->
                    <tr>
                        <td style="background-color: #0a0f0c; padding: 25px 40px; text-align: center; border-top: 1px solid #1a2820;">
                            <p style="margin: 0 0 8px 0; font-size: 11px; color: #5a6b5d; line-height: 1.7; font-family: 'Georgia', serif;">
                                {{ $title ?? 'Ouch Tattoo Studio' }}™ {{ date('Y') }} All rights reserved.
                            </p>
                            <p style="margin: 0; font-size: 10px; color: #475449; line-height: 1.6; font-family: 'Georgia', serif;">
                                You are receiving this correspondence as a valued patron of our establishment.
                            </p>
                        </td>
                    </tr>

                    <!-- Gold Bottom Border -->
                    <tr>
                        <td style="padding: 0;">
                            <div style="height: 3px; background: linear-gradient(90deg, #1a2820 0%, #c9a962 20%, #d4af6a 50%, #c9a962 80%, #1a2820 100%);"></div>
                        </td>
                    </tr>

                </table>

                <!-- Signature Mark -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 650px; margin: 25px auto 0;">
                    <tr>
                        <td style="text-align: center;">
                            <p style="margin: 0; font-size: 11px; color: #5a6b5d; font-style: italic; letter-spacing: 1px; font-family: 'Georgia', serif;">
                                Est. with distinction and artistry
                            </p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>
</html>