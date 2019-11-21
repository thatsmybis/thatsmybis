@extends('layouts.app')
@section('title', 'Calendar - ' . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <iframe src="https://calendar.google.com/calendar/embed?src=omoq9ejr1n77i0bmq3ha7gdh2s%40group.calendar.google.com&ctz=America%2FNew_York"
                style="border: 0"
                width="800"
                height="600"
                frameborder="0"
                scrolling="no"></iframe>
        </div>
    </div>
</div>

@endsection
