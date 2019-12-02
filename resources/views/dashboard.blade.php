@extends('layouts.app')
@section('title', 'Dashboard - ' . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-6 offset-lg-3 mt-3 mb-3">
            @include('partials/createContent')
        </div>

        @foreach ($contents->where('category', 'news') as $content)
            <div class="col-12 col-lg-6 offset-lg-3 mt-3 mb-3">
                @include('partials/content')
            </div>
            @if (!$loop->last)
                <div class="col-12 col-lg-6 offset-lg-3 mt-3 mb-3">
                    <hr class="light">
                </div>
            @endif
        @endforeach
    </div>
</div>

@endsection
