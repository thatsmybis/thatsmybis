<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="utf-8">
    <meta property="og:site_name" content="{{ env('APP_NAME') }}">
    <meta property="og:title" content="@yield('title', env('APP_NAME'))" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:description" content="@yield('description', 'A tool for loot council guilds.')" />
    <meta property="og:determiner" content="" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:locale:alternate" content="en_CA" />
    <meta property="og:locale:alternate" content="en_GB" />
    <meta property="og:image" content="@yield('image', 'https://random-content.s3.amazonaws.com/thatsmybis_logo.jpeg')" />
    <meta property="og:image:url" content="@yield('image', 'https://random-content.s3.amazonaws.com/thatsmybis_logo.jpeg')" />
    <meta property="og:image:type" content="image/png" />
    <meta property="fb:app_id" content="" />

    <!-- JSON-LD markup for schema.org  -->
    <script type="application/ld+json">
    {
        "@context" :    "http://schema.org",
        "@type" :       "Organization",
        "name" :        "{{ env('APP_NAME') }}",
        "image" :       "",
        "description" : "{{ env('APP_NAME') }}",
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
    <meta name="keywords" content="@yield('keywords', "thatsmybis,that's my bis,thatsmybis wow,thatsmybis classic,faerlina,horde,faerlina horde,wow streamers,warcraft streamers,faerlina streamer,faerlina streamers")" />
    <meta name="author" content="https://github.com/Lemmings19/">

    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,600,600i,700,700i&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css"><!-- For fancy select stuff like searching -->
    <link rel="stylesheet" type="text/css" href="/css/main.css">

    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.13.1/css/all.css" integrity="sha384-B9BoFFAuBaCfqw6lxWBZrhg/z4NkwqdBci+E+Sc2XlK/Rz25RYn8Fetb+Aw5irxa" crossorigin="anonymous">

    <!-- Wowhead tooltips and stuff -->
    <script>var whTooltips = {colorLinks: true, iconizeLinks: false, renameLinks: true, iconSize: 'large', hide: { maxstack: true, ilvl: false, sellprice: true }};</script>
    <script src="https://wow.zamimg.com/widgets/power.js"></script>

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
<body class="@yield('bodyClass')">
    @include('layouts/nav')

    @if (session('status'))
        <div class="container-fluid container-width-capped">
            <div class="col-12">
                <div class="alert alert-success">
                    {!! session('status') !!}
                </div>
            </div>
        </div>
    @endif
    @if (session('status-success'))
        <div class="container-fluid container-width-capped">
            <div class="col-12">
                <div class="alert alert-success">
                    {!! session('status-success') !!}
                </div>
            </div>
        </div>
    @endif
    @if (session('status-warning'))
        <div class="container-fluid container-width-capped">
            <div class="col-12">
                <div class="alert alert-warning">
                    {!! session('status-warning') !!}
                </div>
            </div>
        </div>
    @endif
    @if (session('status-danger'))
        <div class="container-fluid container-width-capped">
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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script><!-- Date & time manipulation -->
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script><!-- Tooltip and popover positioning engine... needed for bootstrap and bootstrap-select -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script><!-- For fancy select stuff like searching select inputs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/marked/0.7.0/marked.min.js"></script><!-- Markdown parser -->
    <script src="{{ asset('/js/helpers.js') }}"></script>
    <script src="{{ asset('/js/main.js') }}"></script>
    <script src="{{ asset('/js/autocomplete.js') }}"></script>

    @yield('scripts')

</body>
</html>
