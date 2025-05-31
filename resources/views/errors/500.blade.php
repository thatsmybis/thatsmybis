{{-- resources/views/errors/500.blade.php --}}
@extends('layouts.app')

@section('title', 'Server Error')

@section('content')
    <div class="container-fluid container-width-capped">
        <div class="row">
            <div class="col-12">
                <h1>500 – Server Error</h1>
                <p>Sorry, something went wrong.</p>
                <p>If you cannot visit any pages, try logging out and back in again.</p>
                <p>
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                        <span class="text-grey fal fa-fw fa-sign-out"></span>
                        {{ __("Sign Out") }}
                        <span class="small text-muted">({{ Auth::user()->username }})</span>
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </p>
            </div>
        </div>
    </div>
@endsection
