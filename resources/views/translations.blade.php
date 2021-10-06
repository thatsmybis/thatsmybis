@extends('layouts.app')
@section('title', __('Translations') . ' - ' . config('app.name'))


@section('content')
<div class="container-fluid container-width-capped">
    <div class="row bg-light pt-5 pb-5 mb-5 rounded">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <ul>
                <li>
                    To add or modify translations for your language, message Lemmings19#1149 on the <a href="{{ env('APP_DISCORD') }}" target="_blank">That's My BIS Discord server</a>.
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
