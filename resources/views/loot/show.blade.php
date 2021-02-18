@extends('layouts.app')
@section('title', 'Loot - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium">
                        <span class="fas fa-fw fa-database text-muted"></span>
                        Download WoW Loot Tables
                    </h1>
                </div>
                <div class="col-12 pt-3 pb-1 mb-2 bg-light rounded">
                    <p>
                        <strong>The format of the data being exported is subject to change.</strong> We might change a field or two as development on this project continues.
                    </p>
                    <p>
                        If you absolutely need access to some data or for a specific export format to not change, please reach out on
                        <a href="{{ env('APP_DISCORD') }}" target="_blank" alt="Join the {{ env('APP_NAME') }} Discord Server" title="Join the {{ env('APP_NAME') }} Discord Server" class="">Discord</a>.
                        We will try to help you as best we can.
                    </p>
                    <p>
                        Exports are shown in browser for easy copy+pasting. To save the data, press <kbd>CTRL + S</kbd> or <kbd>CMD + S</kbd>
                    </p>
                    <ol class="no-bullet no-indent striped">
                        @include('partials/expansionDatabases')
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
