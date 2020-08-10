@extends('layouts.app')
@section('title', 'Privacy Policy - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row bg-light pt-5 pb-5 mb-5 rounded">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <h1>
                <span class="fas fa-fw fa-lock"></span>
                Privacy Policy
            </h1>
            <!--
            <p class="text-grey">
                Last revised August 10, 2020
            </p>
            -->

            <ul class="small text-grey">
                <li class="no-bullet">
                    <strong>Change log:</strong>
                </li>
                <li>
                    August 10, 2020 - Document creation date. Google Analytics requires there to be a privacy policy, so here you go.
                </li>
            </ul>

            <p>
                This privacy policy describes the information collected by {{ env('APP_NAME') }} ("{{ env('APP_NAME') }}", "we", "us") and how we use that information.
            </p>

            <h3>Information you provide to us</h3>
            <p>
                We collect information that you directly provide us when you use our services.
            </p>
            <p>
                This includes your Discord username, and anything you save in a textbox on this website.
            </p>

            <h3>Information we collect automatically</h3>
            <p>
                We may temporarily keep server log information when you access and use our services. This may include your IP address, among other connection related information. (this is temporary, and not directly linked to your account)
            </p>
            <p>
                Outside of these temporary server logs, we do not store your IP address  or link it to your account in any way.
            </p>
            <p>
                Anonymous usage information on the website may be collected through the use of a cookie. This may include information such as page views.
            </p>

            <h3>How we use collected information</h3>
            <p>
                To operate the website's core functionality.
            </p>
            <p>
                Information you provide in your guilds should be presumed to be accessible by people in your guild.
            </p>

            <h3>How we share collected information</h3>
            <p>
                People in your guild are able to see stuff you put in that guild.
            </p>
            <p>
                We do not sell or otherwise share information that you directly provide us. It stays securely on the application database and is not distributed.
            </p>
            <p>
                We may disclose information when legally required to do so. For example, in response to a court order, request for cooperation from law enforcement or other government agency, subpoena or similar investigative demand; to establish or exercise our legal rights; to defend against legal claims; or as otherwise required by law. In any of those cases, we may raise any legal objection or right available to us.
            </p>
            <p>
                Aggregated statistical information may be shared. For example, how many users use a given feature, how many users visit the website, etc. (nothing personally identifiable)
            </p>

            <h3>Children under 13</h3>
            <p>
                {{ env('APP_NAME') }} is not intended or directed at people under the age of 13. Therefore, people under the age of 13 may not create an account or otherwise access our services. We do not knowingly collect personal information from people under the age of 13.
            </p>

            <h3>Analytics</h3>
            <p>
                We collect anonymous usage information using <a href="https://www.google.com/policies/privacy/partners/" target="_blank">Google Analytics</a>.
            </p>
            <!--
            <p>
                Registration makes use of Invisible reCAPTCHA which is subject to Google's <a href="https://www.google.com/intl/en/policies/terms/" target="_blank">terms</a> and <a href="https://www.google.com/intl/en/policies/privacy/" target="_blank">privacy policy</a>. reCAPTHCA helps us prevent bots from registering.
            </p>
            -->

            <h3>Information you provide about yourself while using our services</h3>
            <p>
                If you post information on a public section of your profile or guild, we have no control over who sees it, uses it, or collects it. As such, we encourage you to exercise discretion and caution with your personal information.
            </p>

            <h3>Changes to this Privacy Policy</h3>
            <p>
                We will occasionally update this privacy policy. When this privacy policy is updated, we will update the date included at the top of this policy. We recommend checking this policy from time to time to inform yourself of any changes.
            </p>

            <h3>Contact</h3>
            <p>
                Reach out to us <a href="{{ env('APP_DISCORD') }}" target="_blank">on our Discord</a>. (honestly it's just one person who made this website and is running it, so ping Lemmings19#1149 once you're on there and I'll do my best to get back to you in a reasonable timeframe)
            </p>
        </div>
    </div>
</div>
@endsection
