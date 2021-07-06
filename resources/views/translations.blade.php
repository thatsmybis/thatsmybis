@extends('layouts.app')
@section('title', __('Translations') . ' - ' . config('app.name'))


@section('content')
<div class="container-fluid container-width-capped">
    <div class="row bg-light pt-5 pb-5 mb-5 rounded">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <ul>
                <li>
                    To add translations, copy <a href="https://docs.google.com/spreadsheets/d/1DjDx4xQM56X1yofKqA7I2wgQ_rcCzxAemwzTWRn9Ywg/edit?usp=sharing" target="_blank">this spreadsheet</a> and write them. Then send them to my <a href="{{ env('APP_DISCORD') }}" target="_blank">Discord server</a> and I'll get them added.
                </li>
                <li>
                    I'm just one person making this site, and English is the only language I know. But I made it as easy as possible to add translations to the website. Follow the format on <a href="https://docs.google.com/spreadsheets/d/1DjDx4xQM56X1yofKqA7I2wgQ_rcCzxAemwzTWRn9Ywg/edit?usp=sharing" target="_blank">this spreadsheet</a>, then send them to me on the <a href="{{ env('APP_DISCORD') }}" target="_blank">TMB Discord</a>, and I'll get them added to the site.
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
