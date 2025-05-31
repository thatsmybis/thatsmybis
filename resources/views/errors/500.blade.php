{{-- resources/views/errors/500.blade.php --}}
@extends('layouts.app')

@section('title', 'Server Error')

@section('content')
    <div class="container-fluid container-width-capped mt-5 mb-5">
        <div class="row">
            <div class="col-12">
                <h1>500 – Server Error</h1>
                <p>Sorry, something went wrong.</p>
                <p>
                    If you cannot visit any pages, <span class="font-weight-bold">try logging out and back in again</span>:
                    <a href="{{ route('logout') }}"
                        class="font-weight-bold"
                        onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                        <span class="text-grey fal fa-fw fa-sign-out"></span>
                        {{ __("Sign Out") }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </p>
            </div>
        </div>
    </div>
@endsection
