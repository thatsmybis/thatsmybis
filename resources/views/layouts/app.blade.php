<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="utf-8">
    <meta property="og:site_name" content="{{ env('APP_NAME') }}">
    <meta property="og:title" content="@yield('title', env('APP_NAME') . ' WoW Classic Guild')" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:description" content="@yield('description', 'An online gaming community.')" />
    <meta property="og:determiner" content="" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:locale:alternate" content="en_CA" />
    <meta property="og:locale:alternate" content="en_GB" />
    <meta property="og:image" content="@yield('image', 'https://random-content.s3.amazonaws.com/logo.jpeg')" />
    <meta property="og:image:url" content="@yield('image', 'https://random-content.s3.amazonaws.com/logo.jpeg')" />
    <meta property="og:image:type" content="image/png" />
    <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
    <meta property="fb:app_id" content="" />

    <!-- JSON-LD markup for schema.org  -->
    <script type="application/ld+json">
    {
        "@context" :    "http://schema.org",
        "@type" :       "Organization",
        "name" :        "{{ env('APP_NAME') }}",
        "image" :       "",
        "description" : "{{ env('APP_NAME') }} WoW Classic Guild",
        "url" :         "{{ env('APP_URL') }}"
    }
    </script>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}?v=1" />
    <meta name="description" content="@yield('description', 'An online gaming community.')" />
    <meta name="keywords" content="@yield('keywords', 'aftershock, aftershockwow, aftershock wow')" />

    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,600,600i,700,700i&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="/css/main.css">

    @yield('css')

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>

    @yield('headerScripts')

    <noscript>
        For full functionality of this site it is necessary to enable JavaScript.
        Here are the <a href="https://www.enable-javascript.com/" target="_blank">
        instructions how to enable JavaScript in your web browser</a>.
    </noscript>
</head>
<body>
    @include('layouts/nav')

    @if (session('status'))
        <div class="container-fluid">
            <div class="col-12">
                <div class="alert alert-success">
                    {!! session('status') !!}
                </div>
            </div>
        </div>
    @endif
    @if (session('status-success'))
        <div class="container-fluid">
            <div class="col-12">
                <div class="alert alert-success">
                    {!! session('status-success') !!}
                </div>
            </div>
        </div>
    @endif
    @if (session('status-warning'))
        <div class="container-fluid">
            <div class="col-12">
                <div class="alert alert-warning">
                    {!! session('status-warning') !!}
                </div>
            </div>
        </div>
    @endif
    @if (session('status-danger'))
        <div class="container-fluid">
            <div class="col-12">
                <div class="alert alert-danger">
                    {!! session('status-danger') !!}
                </div>
            </div>
        </div>
    @endif


    @yield('content')

    @if (!isset($noFooter))
        @yield('footer')
        @include('layouts/footer')
    @endif

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script><!-- Date & time manipulation -->
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/marked/0.7.0/marked.min.js"></script>
    <script src="{{ asset('/js/helpers.js') }}"></script>
    <script src="{{ asset('/js/main.js') }}"></script>

    @yield('scripts')

</body>
</html>
