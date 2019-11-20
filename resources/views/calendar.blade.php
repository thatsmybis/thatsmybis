@extends('layouts.app')
@section('title', 'Dashboard - ' . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <iframe src="https://calendar.google.com/calendar/embed?src=en.canadian%23holiday%40group.v.calendar.google.com&ctz=America%2FNew_York"
                style="border: 0"
                width="800"
                height="600"
                frameborder="0"
                scrolling="no"></iframe>
        </div>
    </div>
</div>

@endsection
