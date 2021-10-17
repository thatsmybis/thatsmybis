<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="utf-8">
    <meta property="og:site_name" content="{{ env('APP_NAME') }}">
    <meta property="og:title" content="@yield('title', env('APP_NAME'))" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:description" content="@yield('description', 'A tool for World of Warcraft loot management - easily keep track of your raid\'s loot distribution')" />
    <meta property="og:determiner" content="" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:locale:alternate" content="en_CA" />
    <meta property="og:locale:alternate" content="en_GB" />
    <meta property="og:image" content="@yield('image', 'https://random-content.s3.amazonaws.com/thatsmybis_logo.png')" />
    <meta property="og:image:url" content="@yield('image', 'https://random-content.s3.amazonaws.com/thatsmybis_logo.png')" />
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

    <!-- favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/images/favicon/safari-pinned-tab.svg" color="#282447">
    <meta name="msapplication-TileColor" content="#282447">
    <meta name="theme-color" content="#282447">

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('GOOGLE_ANALYTICS_KEY') }}"></script>
    <script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', '{{ env('GOOGLE_ANALYTICS_KEY') }}');</script>


    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha512-MoRNloxbStBcD8z3M/2BmnT+rg4IsMxPkXaGh2zD6LGNNFE80W3onsAhRcMAMrSoyWL9xD7Ert0men7vR8LUZg==" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/jquery.dataTables.min.css" integrity="sha512-1k7mWiTNoyx2XtmI96o+hdjP8nn0f3Z2N4oF/9ZZRgijyV4omsKOXEnqL1gKQNPy2MTSP9rIEWGcH/CInulptA==" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha512-aOG0c6nPNzGk+5zjwyJaoRUgCdOrfSDhmMID2u4+OIslr0GjpLKo7Xm0Ao3xmpM4T8AmIouRkqwj1nrdVsLKEQ==" crossorigin="anonymous" />
    <!-- For fancy select stuff like searching -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css" integrity="sha512-ARJR74swou2y0Q2V9k0GbzQ/5vJ2RBSoCWokg4zkfM29Fb3vZEQyv0iWBMW/yvKgyHSR/7D64pFMmU8nYmbRkg==" crossorigin="anonymous" />
    <!-- styled range slider input -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/css/bootstrap-slider.min.css" integrity="sha512-3q8fi8M0VS+X/3n64Ndpp6Bit7oXSiyCnzmlx6IDBLGlY5euFySyJ46RUlqIVs0DPCGOypqP8IRk/EyPvU28mQ==" crossorigin="anonymous" />
    <!-- datetime picker; native datetimepicker is not supported in all browsers -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g==" crossorigin="anonymous" />

    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.13.1/css/all.css" integrity="sha384-B9BoFFAuBaCfqw6lxWBZrhg/z4NkwqdBci+E+Sc2XlK/Rz25RYn8Fetb+Aw5irxa" crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" href="{{ loadScript('main.css', 'css') }}">

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

    <!-- ads. -->
    <!-- original email <script data-ad-client="ca-pub-2856743447375289" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script> -->
    <script data-ad-client="ca-pub-8209165373319221" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
</head>
<body class="@yield('bodyClass')">
    @include('layouts/nav')

    @if (isset($currentMember) && $currentMember && (isStreamerMode() || $currentMember->raid_group_id_filter))
        <div class="container-fluid container-width-capped">
            <div class="row">
                <div class="col-12">
                    <ul class="list-inline">
                        @if (isStreamerMode())
                            <li class="list-inline-item">
                                <a href="{{ route('toggleStreamerMode') }}" class="small">
                                    <span class="fa-fw fas fa-times"></span>
                                    {{ __("Streamer mode ON") }}
                                </a>
                            </li>
                        @endif

                        @if($currentMember->raid_group_id_filter)
                            <li class="list-inline-item">
                                <form role="form" method="POST" action="{{ route('setRaidGroupFilter', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                                    {{ csrf_field() }}
                                    <input hidden name="raid_group_id" value="" />
                                    <button class="link">
                                        <span class="small">
                                            <span class="fa-fw fas fa-times"></span>
                                            {{ __("Attendance filter:") }} {{ $guild->allRaidGroups->where('id', $currentMember->raid_group_id_filter)->first()->name }}
                                        </span>
                                    </button>
                                </form>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="text-center font-weight-bold text-warning">
        <!-- Sitewide warning message, good for downtime announcements -->
        <span class="text-muted">
            <!--I added a lot of caching. If you notice problems, let me know on the <a href="{{ env('APP_DISCORD') }}" target="_blank">TMB Discord</a>.-->
        </span>
    </div>

    @if (session('status'))
        <div class="container-fluid container-width-capped">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-success">
                        {!! session('status') !!}
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (session('status-danger'))
        <div class="container-fluid container-width-capped">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger">
                        {!! session('status-danger') !!}
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (session('status-warning'))
        <div class="container-fluid container-width-capped">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-warning">
                        {!! session('status-warning') !!}
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (session('status-success'))
        <div class="container-fluid container-width-capped">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-success">
                        {!! session('status-success') !!}
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (isset($guild) && $guild->message)
        <div class="container-fluid container-width-capped">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info">
                        <ul class="no-bullet no-indent mb-0">
                            <li class="small font-weight-bold">
                                {{ __("Message of the Day") }}
                            </li>
                            <li class="pre">{{ $guild->message }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif


    @yield('content')

    @if (!isset($noFooter))
        @yield('footer')
        @include('layouts/footer')
    @endif

    <!-- Button that sticks to bottom right of page -->
    <a id="reportBug" href="{{ env('APP_DISCORD') }}" target="_blank" class="btn btn-sm btn-light" title="Report a bug">
        {{ __("Give Feedback") }} <span class="text-success fal fa-fw fa-comment-dots"></span>
    </a>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous"></script>
    <!-- Date & time manipulation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous"></script>
    <!-- Optionally, moment has ALL locales but is ~361kb instead of ~58kb. Individual locale files are ~5kb -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js" integrity="sha512-LGXaggshOkD/at6PFNcp2V2unf9LzFq6LE+sChH7ceMTDP0g2kn6Vxwgg7wkPP7AAtX+lmPqPdxB47A0Nz0cMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    @if (Illuminate\Support\Facades\App::getLocale() != 'en')
        @if(Illuminate\Support\Facades\App::getLocale() === 'da')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/da.min.js" integrity="sha512-rybyYvSnwg3zAZ/vjTTMoh8tOK0Z6tC2XCzr9e27M0xr8WU40IGo6SOP7FXXCyWgMyd3wZ8ln2nY4ce1ysYUfw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        @elseif(Illuminate\Support\Facades\App::getLocale() === 'de')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/de.min.js" integrity="sha512-2xPqgWwEg9s0xYZzGEXtrNPzReBmd9u7ZNv2g9zcJxW7zOONVdZHhtlUC4lSJ/dG0Nf7Nh366o/yxatDTyofSA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        @elseif(Illuminate\Support\Facades\App::getLocale() === 'es')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/es.min.js" integrity="sha512-L6Trpj0Q/FiqDMOD0FQ0dCzE0qYT2TFpxkIpXRSWlyPvaLNkGEMRuXoz6MC5PrtcbXtgDLAAI4VFtPvfYZXEtg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        @elseif(Illuminate\Support\Facades\App::getLocale() === 'fr')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/fr.min.js" integrity="sha512-RAt2+PIRwJiyjWpzvvhKAG2LEdPpQhTgWfbEkFDCo8wC4rFYh5GQzJBVIFDswwaEDEYX16GEE/4fpeDNr7OIZw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        @elseif(Illuminate\Support\Facades\App::getLocale() === 'it')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/it.min.js" integrity="sha512-abyAPza1Q/3PRl2L54rOvygrx/XIkupvWrs7sNm+jD6gfNf3+MEvPJzdSG4LyYWSTA8NY7AnTCnRz5NNyvsg0w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        @elseif(Illuminate\Support\Facades\App::getLocale() === 'no')
            <!-- No support, sorry! -->
        @elseif(Illuminate\Support\Facades\App::getLocale() === 'pl')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/pl.min.js" integrity="sha512-4Hra0ugHwC1jKVrS6cwYQu47pRQxNoZZNT/KKLraGJb4csT6rxfba0jpIxKE1O7N5ImwPKqbYv875hXN5h0tqA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        @elseif(Illuminate\Support\Facades\App::getLocale() === 'pt')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/pt.min.js" integrity="sha512-l3UpBpQozVlOQB23Ah65fE1Y4u5XMGpaITEybm4NyK06ISM5OVaUWs50wQ7h3IzbNiozaP9HkNYUIy0mGDNS7Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        @elseif(Illuminate\Support\Facades\App::getLocale() === 'ru')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/ru.min.js" integrity="sha512-+yvkALwyeQtsLyR3mImw8ie79H9GcXkknm/babRovVSTe04osQxiohc1ukHkBCIKQ9y97TAf2+17MxkIimZOdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        @elseif(Illuminate\Support\Facades\App::getLocale() === 'ko')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/ko.min.js" integrity="sha512-3kMAxw/DoCOkS6yQGfQsRY1FWknTEzdiz8DOwWoqf+eGRN45AmjS2Lggql50nCe9Q6m5su5dDZylflBY2YjABQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        @elseif(Illuminate\Support\Facades\App::getLocale() === 'cn')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/zh-cn.min.js" integrity="sha512-j83eLbbs+KVKlv8KI6i2lWPlLGY1nltBDnWXIMedQYYjhd5sfifdJB6f2Wxdli5mfrNqRbESVpqSXDHhzMREGw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        @endif
    @endif

    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js" integrity="sha512-BkpSL20WETFylMrcirBahHfSnY++H2O1W+UnEEO4yNIl+jI2+zowyoGJpbtk6bx97fBXf++WJHSSK2MV4ghPcg==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables-fixedheader/3.1.9/dataTables.fixedHeader.min.js" integrity="sha512-gmPh+Otcht/SQFZF9IWTrVYCmIIz5ZUS3TpNdhxyRnWrQXcBK96mivWWHMg2BkL4vScTk0qbgp0uGcVY6DnLbg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Tooltip and popover positioning engine... needed for bootstrap and bootstrap-select -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js" integrity="sha512-ubuT8Z88WxezgSqf3RLuNi5lmjstiJcyezx34yIU2gAHonIi27Na7atqzUZCOoY4CExaoFumzOsFQ2Ch+I/HCw==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha512-M5KW3ztuIICmVIhjSqXe01oV2bpe248gOxqmlcYrEzAvws7Pw3z6BK0iGbrwvdrUQUhi3eXgtxp5I8PDo9YfjQ==" crossorigin="anonymous"></script>
    <!-- For fancy select stuff like searching select inputs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js" integrity="sha512-yDlE7vpGDP7o2eftkCiPZ+yuUyEcaBwoJoIhdXv71KZWugFqEphIS3PU60lEkFaz8RxaVsMpSvQxMBaKVwA5xg==" crossorigin="anonymous"></script>
    <!-- HTML santizer -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.2.9/purify.min.js" integrity="sha512-VOTFfd4vxPpuE98m9UQTN7j2N2/Blw44lZMhRTnZQqA2gOHq7Yvn5bdIDjjAZuFqqkDtppcjMW+dN3GYbhM94g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Markdown parser -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/marked/2.0.0/marked.min.js" integrity="sha512-9xs4M7yksWUBIo+Jhfs65z7U4QTKNuVeN6Desvi2nXREybeQB3RfTBDELEcXD1HaNlnshG87a1SijMShvp4Zcw==" crossorigin="anonymous"></script>
    <!-- CSV parser -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js" integrity="sha512-rKFvwjvE4liWPlFnvH4ZhRDfNZ9FOpdkD/BU5gAIA3VS3vOQrQ5BjKgbO3kxebKhHdHcNUHLqxQYSoxee9UwgA==" crossorigin="anonymous"></script>
    <!-- styled range input slider -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/bootstrap-slider.min.js" integrity="sha512-f0VlzJbcEB6KiW8ZVtL+5HWPDyW1+nJEjguZ5IVnSQkvZbwBt2RfCBY0CBO1PsMAqxxrG4Di6TfsCPP3ZRwKpA==" crossorigin="anonymous"></script>
    <!-- datetime picker; native datetimepicker is not supported in all browsers -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js" integrity="sha512-AIOTidJAcHBH2G/oZv9viEGXRqDNmfdPVPYOYKGy3fti0xIplnlgMHUGfuNRzC6FkzIo0iIxgFnr9RikFxK+sw==" crossorigin="anonymous"></script>
    <!-- draggable stuff now works on mobile -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js" integrity="sha512-0bEtK0USNd96MnO4XhH8jhv3nyRF0eK87pJke6pkYf3cM0uDIhNJy9ltuzqgypoIFXw3JSuiy04tVk4AjpZdZw==" crossorigin="anonymous"></script>

    <!-- Wowhead tooltips and stuff -->
    <script>var whTooltips = {colorLinks: true, iconizeLinks: true, renameLinks: true, dropChance: true, iconSize: '@yield('wowheadIconSize', 'small')', hide: { maxstack: true, ilvl: false, sellprice: true }};</script>
    <script src="https://wow.zamimg.com/widgets/power.js"></script>
    <style>/* override some wowhead item quality colors to be higher contrast */.q3 {color: #057ffa !important;} .q4 {color: #ab4aed !important;}</style>

    <script>
        @if (isset($guild) && $guild->expansion_id)
            var expansionId = {{ $guild->expansion_id }};
            @if ($guild->expansion_id === 1)
                var wowheadSubdomain = "classic";
            @elseif ($guild->expansion_id === 2)
                var wowheadSubdomain = "tbc";
            @elseif ($guild->expansion_id === 3)
                var wowheadSubdomain = "www";
            @else
                var wowheadSubdomain = "www";
            @endif
        @elseif (isset($expansionId))
            var expansionId = {{ $expansionId }};
            @if ($expansionId == 1)
                var wowheadSubdomain = "classic";
            @elseif ($expansionId == 2)
                var wowheadSubdomain = "tbc";
            @elseif ($expansionId == 3)
                var wowheadSubdomain = "www";
            @else
                var wowheadSubdomain = "www";
            @endif
        @else
            var expansionId = 1;
            var wowheadSubdomain = "www";
        @endif

        @if (Illuminate\Support\Facades\App::getLocale() != 'en')
            var locale = "{{ Illuminate\Support\Facades\App::getLocale() }}";
            var wowheadLocale = locale  + ".";

            // Doing this was faster than adding translations to the entirety of JS
            var headerBoss      = "{{ __("Boss") }}";
            var headerLoot      = "{{ __("Loot") }}";
            var headerCharacter = "{{ __("Character") }}";
            var headerPrios     = "{{ __("Prios") }}";
            var headerWishlist  = "{{ __("Wishlist") }}";
            var headerReceived  = "{{ __("Received") }}";
            var headerRecipes   = "{{ __("Recipes") }}";
            var headerNotes     = "{{ __("Notes") }}";
            var headerPrioNotes = "{{ __("Prios Notes") }}";

            var localeAlt = "{{ __("alt") }}";
        @else
            var locale = "";
            var wowheadLocale = "";

            var headerBoss      = "Boss";
            var headerCharacter = "Character";
            var headerPrios     = "Prios";
            var headerPrioNotes = "Prio Notes";
            var headerWishlist  = "Wishlist";
            var headerLoot      = "Loot";
            var headerReceived  = "Received";
            var headerRecipes   = "Recipes";
            var headerNotes     = "Notes";

            var localeAlt = "alt";
        @endif
    </script>

    <script src="{{ loadScript('helpers.js') }}"></script>
    <script src="{{ loadScript('autocomplete.js') }}"></script>

    @yield('scripts')
</body>
</html>
