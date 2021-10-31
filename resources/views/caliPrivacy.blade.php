@extends('layouts.app')
@section('title', __("Privacy Policy") . ' - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row bg-light pt-5 pb-5 mb-5 rounded">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <h1>
                <span class="fas fa-fw fa-lock"></span>
                {{ __("Privacy Policy") }} Additional Disclosures for California Residents
            </h1>

            <p>
                These additional disclosures apply only to California residents. The California Consumer Privacy Act of 2018 ("CCPA") provides additional rights to know, delete and opt-out, and requires businesses collecting or disclosing personal information to provide notices and means to exercise rights. For our full Privacy Policy, please visit our <a href="{{ route('privacy') }}">privacy policy</a>.
            </p>

            <h3>1.1 Notice of Collection.</h3>

            <p>
                In the past 12 months, we have <strong>or may have</strong> collected the following categories of personal information enumerated in the CCPA:
            </p>

            <ul>
                <li>Identifiers, including online identifiers (such as IP address).</li>
                <li>Customer records, including phone number, billing address, and credit or debit card information.</li>
                <li>Commercial or transactions information, including records of products or services purchased, obtained, or considered.</li>
                <li>Internet activity, including browsing history, search history, and interactions with a website, email, application, or advertisement.</li>
                <li>Geolocation data.</li>
                <li>Inference drawn from the above information about your predicted characteristics and preferences.</li>
            </ul>

            <p>
                For further details on information we collect, including the sources from which we have received information in the past 12 months, review the "Information you provide to us" and "Information we collect automatically" sections in our Privacy Policy. We collect and use these categories of personal information for the business purposes described in the "How do we use collected information" section in our Privacy Policy.
            </p>

            <p>
                Under the CCPA, "sell" is defined broadly, and some of our data sharing practices may be considered a "sale." To the extent "sale" under the CCPA is interpreted to include the activities set out in our Privacy Policy, such as those disclosed our Privacy Policy, we will comply with applicable law as to such activity. We disclose the following categories of personal information for commercial purposes: identifiers, characteristics, commercial or transactions information, internet activity, geolocation data, and inferences drawn. Please review our Privacy Policy for further details about the categories of parties with whom we have shared information in the past 12 months.
            </p>

            <h3>1.2 Right to Know and Delete.</h3>

            <p>
                You have the right to know certain details about our data practices in the past 12 months. In particular, you may request the following from us:
            </p>

            <ul>
                <li>The categories of personal information we have collected about you;</li>
                <li>The categories of sources from which the personal information was collected;</li>
                <li>The categories of personal information about you we disclosed for a business purpose or sold;</li>
                <li>The categories of third parties to whom the personal information was disclosed for a business purposes or sold;</li>
                <li>The business or commercial purpose for collecting or selling the personal information; and</li>
                <li>The specific pieces of personal information we have collected about you.</li>
            </ul>

            <p>
                In addition, you have the right to delete the personal information we have collected from you.
            </p>

            <p>
                To exercise any of these rights, please submit a request through our <a href="{{ env('APP_DISCORD') }}" target="_blank">Discord server</a> or direct message us on Discord. In the request, please specify which right you are seeking to exercise and the scope of the request. We will confirm receipt of your request within 10 days. We may require specific information from you to help us verify your identity and process your request. If we are unable to verify your identity, we may deny your requests to know or delete.
            </p>

            <h3>1.3 Right to Opt-Out.</h3>

            <p>
                To the extent That's My BIS sells your personal information as the term "sell" is defined under the CCPA, you have the right to opt-out of the sale of your personal information by us to third parties at any time. You may submit a required to opt-out by clicking Do Not Sell My Personal Information.
            </p>

            <h3>1.4 Authorized Agent.</h3>

            <p>
                You can designate an authorized agent to submit requests on your behalf. However, we will require written proof of the agent’s permission to do so and verify you identity directly.
            </p>

            <h3>1.5 Right to Non-Discrimination.</h3>

            <p>
                You have the right not to receive discriminatory treatment by us for the exercise of any of your rights.
            </p>

            <h3>1.6 Shine the Light.</h3>

            <p>
                Customers who are residents of California may request (i) a list of the categories of personal information disclosed by us to third parties during the immediately preceding calendar year for those third parties’ own direct marketing purposes; and (ii) a list of the categories of third parties to whom we disclosed such information. To exercise a request, contact or direct message us at our <a href="{{ env('APP_DISCORD') }}" target="_blank">Discord server</a> (Attention: Privacy) and specify that you are making a "California Shine the Light Request." We may require additional information from you to allow us to verify your identity and are only required to respond to requests once during any calendar year.
            </p>

            <h3>1.7 Minors.</h3>

            <p>
                That's My BIS does not knowingly "sell," as that term is defined under the CCPA, the personal information of minors under 16 years old who are California residents. If you are a California resident under 18 years old and registered to use the Services, you can ask us to remove any content or information you have posted on the Services. To make a request, contact or direct message us at our <a href="{{ env('APP_DISCORD') }}" target="_blank">Discord server</a> with "California Under 18 Content Removal Request" in your message, and tell us what you want removed. We will make reasonable good faith efforts to remove the post from prospective public view, although we cannot ensure the complete or comprehensive removal of the content and may retain the content as necessary to comply with our legal obligations, resolve disputes, and enforce our agreements.
            </p>

        </div>
    </div>
</div>
@endsection
