@extends('layouts.app')
@section('title', 'Calendar - ' . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-12 text-center" style="height: 88vh; min-height: 500px; min-width: 625px;">
            <iframe src="http://{{ $_SERVER['SERVER_NAME'] }}/{{ $guild->slug }}/calendar/iframe"
                style="border: 0; height: 100%; width: 100%;"
                frameborder="0"
                scrolling="no"></iframe>
        </div>
    </div>
</div>

@endsection
