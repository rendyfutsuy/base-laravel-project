<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email Verification Code</title>
    <style>
        @font-face {
            font-size: 20px;
            font-family: "Montserrat";
            font-weight: normal;
            src: url("https://fonts.googleapis.com/css2?family=Montserrat");
        }

        body {
            font-family:'Montserrat',Sans-Serif,serif;
        }

        @media screen and (max-width: 767px) and (orientation: portrait)  {
            .content {
                width: 85vw !important;
                background-size: 100% !important;
            }

            .email-title {
                font-size: 26px !important;
            }

            .email-code {
                font-size: 24px !important;
            }

            .email-code {
                font-size: 26px !important;
            }

            .content-container {
                margin: 10px 25px !important;
            }

            .subtitle {
                color: white;
                font-size: 15px !important;
                margin-bottom: 10px !important;
            }

            .verify-btn {
                margin-top: 20px !important;
                margin-bottom: 20px !important;
                width: auto !important;
                font-size: 12px !important;
                padding: 15px 0 !important;
            }
        }
    </style>
</head>

<body>
<div style="display:flex;flex-wrap:wrap;align-items:center;justify-content: center;">
    <div
        class="content"
        style="background-color: #212121;color: #FFFFFF;width: 60vw;background-image: url({{ config('mail.background_img')  }});
        background-size: 65%;background-position: right top;background-repeat: no-repeat"
    >
        @include('mails.components.header')
        <div class="content-container" style="margin: 15px 150px;font-family:'Montserrat',Sans-Serif,serif;">
            <p class="email-title" style="font-size: 36px;font-weight: 700;text-align: center;margin-bottom: 30px; letter-spacing: 2px;">
                Email Verification Code
            </p>
            <p class="email-code" style="font-size: 24px;font-weight: 600;text-align: center;margin-bottom: 30px; letter-spacing: 2px;">
                Verification Code :
            </p>
            <p class="code-otp" style="font-size: 36px;font-weight: 700;text-align: center;margin-bottom: 30px; letter-spacing: 2px;">
                {{ $otp }}
            </p>
            <p class="subtitle" style="font-size: 20px; font-weight: bolder; margin-bottom: 30px;">
                Hi {{ $to->email }},
            </p>
            <p class="subtitle" style="font-size: 20px; margin-bottom: 30px;">
                Please return to the registration page and insert the <br>
                code above to verify your account
            </p>

            <p class="subtitle" style="font-size: 20px; font-weight: lighter; margin-bottom: 30px;">
                Best regards,
                <br>
                <b>Rendy Terumi Team</b>
            </p>
            @include('mails.components.contact_us')
        </div>
        @include('mails.components.footer')
    </div>
</div>
</body>
</html>
