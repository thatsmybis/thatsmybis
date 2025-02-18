@php
    $rand = mt_rand();
@endphp
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
    <meta name="csrf-token" content="">

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

    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-rqn26AG5Pj86AF4SO72RK5fyefcQ/x32DNQfChxWvbXIyXFePlEktwD18fEz+kQU" crossorigin="anonymous">

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

    <!-- AdSense -->
    <script data-ad-client="ca-pub-8209165373319221" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <!-- Ads -->
    <script>
    window.nitroAds=window.nitroAds||{createAd:function(){window.nitroAds.queue.push(["createAd",arguments])},addUserToken:function(){window.nitroAds.queue.push(["addUserToken",arguments])},queue:[]};
    </script>
    <script async src="https://s.nitropay.com/ads-921.js"></script>
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

    <div class="text-center text-warning">
        <!-- Sitewide warning message, good for downtime announcements -->
        <!-- Site will be down for maintenance <span class="js-timestamp" data-timestamp="1697036400" data-format="ddd, MMM Do YYYY @ h:mm a"></span> -->
        <span class="font-weight-bold text-gold"></span>
        @if (isset($guild) && $guild->expansion_id === 4)
            <span class=""></span>
        @elseif (isset($guild) && $guild->expansion_id === 5)
            <span class="small">
                Cata loot tables were put together manually; report errors
                <a href="{{ env('APP_DISCORD') }}" target="_blank" title="{{ __('Report a bug') }}">here</a>
            </span>
    </a>
        @endif
    </div>

    @if (isset($guild))
        <div id="addonPrompt" class="small text-center text-success" style="display:none;">
            Choose an addon to use with TMB:
            <a href="https://www.curseforge.com/wow/addons/gargul" target="_blank" class="text-legendary">Gargul</a> or
            <a href="https://www.curseforge.com/wow/addons/rclootcouncil-classic" target="_blank" class="text-legendary">RCLC</a>
            <span id="closeAddonPrompt" class="cursor-pointer small text-muted fa-fw fas fa-times"></span>
        </div>
    @endif

    @if (isset($guild) && $guild->expansion_id < 4)
        <div class="text-center font-weight-normal text-muted mb-2">
            To use <span class="font-weight-semibold text-legendary">Cata</span>, register a new guild in <a href="{{ isset($guild) ? route('guild.settings', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) : route('home') }}">Guild Settings</a> or the <a href="{{ route('home') }}">Dashboard</a>
            <span class="text-muted">
            </span>
        </div>
    @endif

    @if (isset($guild) && $guild->expansion_id === 4 && time() < 1728302400)
        <div class="text-center font-weight-normal text-muted mb-2">
            <span class="font-weight-semibold text-gold">
                Lord Thunderaan has been added to
                <a href="{{ route('guild.item.list', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'instanceSlug' => 'world-bosses-sod']) }}#thunderaan">
                    {{ __("World Bosses") }}
                </a>
            </span>
        </div>
    @endif

    @if (isset($guild) && in_array(Illuminate\Support\Facades\App::getLocale(), ['de', 'es', 'fr', 'pt', 'ru', 'ko', 'cn']))
        <div class="text-center font-weight-normal text-muted mb-2">
            You can now lookup items using your language. <span class="small">previously only English</span>
        </div>
    @endif

    <!-- For announcements
    <div class="text-center font-weight-light small text-muted text-mage mb-2">
    </div>
    -->

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

    @if (!request()->get('hideAds'))
        <div id="{{ $rand }}" class="container-fluid mb-3 " style="display:none;">
            <div class="row">
                <div class="col-12">
                    <div class="ml-3 mr-3 p-3 bg-light rounded" style="max-width: 800px;">
                        You appear to be using an ad blocker.
                        <br>
                        This site is operated independently and relies on ad revenue and donations.
                        <br>
                        Please consider supporting on <a href="https://www.patreon.com/lemmings19" target="_blank">Patreon</a> or disabling your ad blocker on this website.
                        <br>
                        <span class="smaller text-muted">If you support on Patreon please link your Discord account or message me!</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-poster-970 mb-4" id="top-large-leaderboard-poster"></div>
        <div class="d-poster-728 mb-4" id="top-leaderboard-poster"></div>
        <div class="d-poster-320 mb-4" id="top-mobile-banner-poster"></div>
    @endif

    @yield('content')

    @if (!request()->get('hideAds'))
        <div class="d-poster-970" id="bottom-large-billboard-poster"></div>
        <div class="d-poster-728" id="bottom-leaderboard-poster"></div>
        <div class="d-poster-320" id="bottom-mobile-banner-poster"></div>
    @endif

    @if (!isset($noFooter))
        @yield('footer')
        @include('layouts/footer')
    @endif

    @if (!request()->get('hideAds'))
        <div id="video-poster"></div>
    @endif

    <!-- Button that sticks to bottom right of page -->
    <span id="toTopOfPage" style="display:none;" class="btn btn-sm btn-light" title="{{ __('Go to top of page') }}">
        {{ __("Top") }} <span class="text-success fal fa-fw fa-arrow-up"></span>
    </span>
    <a id="reportBug" href="{{ env('APP_DISCORD') }}" target="_blank" class="btn btn-sm btn-light" title="{{ __('Report a bug') }}">
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
    <!-- Cookie manipulation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.1/js.cookie.min.js" integrity="sha512-wT7uPE7tOP6w4o28u1DN775jYjHQApdBnib5Pho4RB0Pgd9y7eSkAV1BTqQydupYDB9GBhTcQQzyNMPMV3cAew==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Wowhead tooltips and stuff -->
    <script>var whTooltips = {colorLinks: true, iconizeLinks: @yield('wowheadShowIcons', 'true'), renameLinks: true, dropChance: true, iconSize: '@yield('wowheadIconSize', 'small')', hide: { maxstack: true, ilvl: false, sellprice: true }};</script>
    <script src="https://wow.zamimg.com/widgets/power.js"></script>
    <style>/* override some wowhead item quality colors to be higher contrast */.q3 {color: #057ffa !important;} .q4 {color: #b562ee !important;}</style>

    <script>
        @if (isset($guild) && $guild->expansion_id)
            var expansionId = {{ $guild->expansion_id }};
            @if ($guild->expansion_id === 1)
                var wowheadSubdomain = "classic";
            @elseif ($guild->expansion_id === 2)
                var wowheadSubdomain = "tbc";
            @elseif ($guild->expansion_id === 3)
                var wowheadSubdomain = "wotlk";
            @elseif ($guild->expansion_id === 4)
                var wowheadSubdomain = "classic";
            @elseif ($guild->expansion_id === 5)
                var wowheadSubdomain = "cata";
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
                var wowheadSubdomain = "wotlk";
            @elseif ($expansionId == 4)
                var wowheadSubdomain = "classic";
            @elseif ($expansionId == 5)
                var wowheadSubdomain = "cata";
            @else
                var wowheadSubdomain = "www";
            @endif
        @else
            var expansionId = 1;
            var wowheadSubdomain = "www";
        @endif

        @if (isset($guild) && $guild->faction)
            var faction = "{{ $guild->faction }}";
        @else
            var faction = null;
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

        $(document).ready(function () {
            $(document).scroll(function() {
                const y = $(this).scrollTop();
                if (y > 2000) {
                    $("#toTopOfPage").fadeIn();
                } else {
                    $("#toTopOfPage").fadeOut();
                }
            });
            $("#toTopOfPage").click(function () {
                scroll(0,0);
            });

            const CLOSED_ADDON_COOKIE = "closedAddonPrompt";
            const addonPromptCookie = Cookies.get(CLOSED_ADDON_COOKIE);

            if (!addonPromptCookie) {
                $("#addonPrompt").show();
            }

            $("#closeAddonPrompt").click(function () {
                $("#addonPrompt").hide();
                Cookies.set(CLOSED_ADDON_COOKIE, 1, { expires: 30 });
            });
        });
        function restoreAddonPrompt() {
            Cookies.remove(CLOSED_ADDON_COOKIE);
        }
    </script>

    <script src="{{ loadScript('helpers.js') }}"></script>
    <script src="{{ loadScript('autocomplete.js') }}"></script>

    <script>
        <!-- NitroPay GDPR preferences https://docs.nitropay.com/en/articles/3623674-creating-a-link-to-open-gdpr-preferences -->
        if (document.getElementById("consent-box")) {
            if (window["nitroAds"] && window["nitroAds"].loaded) {
                document.getElementById("consent-box").style.display = (window["__tcfapi"] ? "" : "none");
            } else {
                document.addEventListener(
                    "nitroAds.loaded",
                    () => document.getElementById("consent-box").style.display = (window["__tcfapi"] ? "" : "none")
                );
            }
        }
        window['nitroAds'].createAd('video-poster-floating', {
            "demo": {{ env('EXAMPLE_ADS', 'false') }},
            "format": "floating",
            "refreshTime": 30,
            "report": {
                "enabled": true,
                "icon": true,
                "wording": "Report Ad",
                "position": "bottom-right"
            },
            "video": {}
        });
        window['nitroAds'].createAd('top-large-leaderboard-poster', {
            "demo": {{ env('EXAMPLE_ADS', 'false') }},
            "refreshLimit": 0,
            "refreshTime": 60,
            "renderVisibleOnly": true,
            "refreshVisibleOnly": true,
            "sizes": [
                [
                    "970",
                    "90"
                ]
            ],
            "report": {
                "enabled": true,
                "wording": "Report Ad",
                "position": "bottom-right"
            },
            // "mediaQuery": "(min-width: 1000px)"
        });
        window['nitroAds'].createAd('bottom-large-billboard-poster', {
            "demo": {{ env('EXAMPLE_ADS', 'false') }},
            "refreshLimit": 0,
            "refreshTime": 60,
            "renderVisibleOnly": true,
            "refreshVisibleOnly": true,
            "sizes": [
                [
                    "970",
                    "250"
                ]
            ],
            "report": {
                "enabled": true,
                "wording": "Report Ad",
                "position": "bottom-right"
            },
            // "mediaQuery": "(min-width: 1000px)"
        });
        window['nitroAds'].createAd('top-leaderboard-poster', {
            "demo": {{ env('EXAMPLE_ADS', 'false') }},
            "refreshLimit": 0,
            "refreshTime": 60,
            "renderVisibleOnly": true,
            "refreshVisibleOnly": true,
            "sizes": [
                [
                    "728",
                    "90"
                ]
            ],
            "report": {
                "enabled": true,
                "wording": "Report Ad",
                "position": "bottom-right"
            },
            // "mediaQuery": "(min-width: 768px) and (max-width: 999px)"
        });
        window['nitroAds'].createAd('bottom-leaderboard-poster', {
            "demo": {{ env('EXAMPLE_ADS', 'false') }},
            "refreshLimit": 0,
            "refreshTime": 60,
            "renderVisibleOnly": true,
            "refreshVisibleOnly": true,
            "sizes": [
                [
                    "728",
                    "90"
                ]
            ],
            "report": {
                "enabled": true,
                "wording": "Report Ad",
                "position": "bottom-right"
            },
            // "mediaQuery": "(min-width: 768px) and (max-width: 999px)"
        });
        window['nitroAds'].createAd('top-mobile-banner-poster', {
            "demo": {{ env('EXAMPLE_ADS', 'false') }},
            "refreshLimit": 0,
            "refreshTime": 60,
            "renderVisibleOnly": true,
            "refreshVisibleOnly": true,
            "sizes": [
                [
                    "320",
                    "50"
                ]
            ],
            "report": {
                "enabled": true,
                "wording": "Report Ad",
                "position": "bottom-right"
            },
            // "mediaQuery": "(max-width: 767px)"
        });
        window['nitroAds'].createAd('bottom-mobile-banner-poster', {
            "demo": {{ env('EXAMPLE_ADS', 'false') }},
            "refreshLimit": 0,
            "refreshTime": 60,
            "renderVisibleOnly": true,
            "refreshVisibleOnly": true,
            "sizes": [
                [
                    "320",
                    "50"
                ]
            ],
            "report": {
                "enabled": true,
                "wording": "Report Ad",
                "position": "bottom-right"
            },
            // "mediaQuery": "(max-width: 767px)"
        });
        window['nitroAds'].createAd('left-banner', {
            "demo": {{ env('EXAMPLE_ADS', 'false') }},
            "refreshLimit": 0,
            "refreshTime": 60,
            "renderVisibleOnly": false,
            "refreshVisibleOnly": true,
            "sizes": [
                [
                    "160",
                    "600"
                ]
            ],
            "report": {
                "enabled": true,
                "icon": true,
                "wording": "Report Ad",
                "position": "bottom-right"
            }
        });
        window['nitroAds'].createAd('right-banner', {
            "demo": {{ env('EXAMPLE_ADS', 'false') }},
            "refreshLimit": 0,
            "refreshTime": 60,
            "renderVisibleOnly": false,
            "refreshVisibleOnly": true,
            "sizes": [
                [
                    "160",
                    "600"
                ]
            ],
            "report": {
                "enabled": true,
                "icon": true,
                "wording": "Report Ad",
                "position": "bottom-right"
            }
        });
    </script>
    <script type="text/javascript">
        // Adblock detection
        document.addEventListener('blockerEvent', (event) => {
            // The user is blocking ads
            $("#{{ $rand }}").show();
        });

        function dispatchBlockerEvent() {
            if (document.dispatchEvent && window.CustomEvent) {
                document.dispatchEvent(new CustomEvent('blockerEvent'));
            }
        }

        // Try to load a nitropay ad
        var npDetect = new (function () {
            this.blocking = false;
            var errcnt = 0;
            function testImg() {
                var i = new Image();
                i.onerror = () => {
                    errcnt++;
                    if (errcnt < 3) {
                        setTimeout(testImg, 10);
                    } else {
                        npDetect.blocking = true;
                        dispatchBlockerEvent();
                    }
                };
                i.onload = () => {
                    npDetect.blocking = false;
                };

                i.src = 'https://s.nitropay.com/1.gif?' + Math.random() + '&adslot=';
            }
            testImg();
        })();

        window.addEventListener("load", () => {
            setTimeout(function() {
                const ad = document.querySelector('ins.adsbygoogle');
                if (!ad) {
                    // Ads blocked
                    dispatchBlockerEvent();
                }
            }, 400);
        });
    </script>

    @yield('scripts')
</body>
</html>
