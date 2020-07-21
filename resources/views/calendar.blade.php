@extends('layouts.app')
@section('title', 'Calendar - ' . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="row pt-2 mb-3">
        <div class="col-12 mt-2 mb-2">
            <h2 class="font-weight-medium">
                <span class="text-uncommon">
                    &lt;{{ $guild->name }}&gt;
                </span>
                Calendar
            </h2>
        </div>
    </div>
    <div class="row">
        <div class="col-12 text-center" style="height: 88vh; min-height: 500px; min-width: 625px;">
            <iframe src="http://{{ $_SERVER['SERVER_NAME'] }}/{{ $guild->slug }}/calendar/iframe"
                style="border: 0; height: 100%; width: 100%;"
                frameborder="0"
                scrolling="no"></iframe>
        </div>
    </div>
</div>

@endsection
