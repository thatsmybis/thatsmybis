@extends('layouts.app')
@section('title', 'News - ' . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-6 offset-lg-3">
            @include('partials/createContent')
        </div>

        @if ($contents->count() > 0)
            @foreach ($contents as $content)
                <div class="col-12 col-lg-6 offset-lg-3">
                    @include('partials/content')
                </div>
                @if (!$loop->last)
                    <div class="col-12 col-lg-6 offset-lg-3">
                        <hr class="light">
                    </div>
                @endif
            @endforeach
        @else
            <div class="col-12 col-lg-6 offset-lg-3">
                Nothing to see here. Move along now, move along.
            </div>
        @endif
    </div>
</div>

@endsection
