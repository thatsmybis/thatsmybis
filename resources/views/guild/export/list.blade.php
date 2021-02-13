@extends('layouts.app')
@section('title', 'Export Data - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium">
                        <span class="fas fa-fw fa-database text-muted"></span>
                        Choose Data to Export
                    </h1>
                </div>
                <div class="col-12 pt-3 pb-1 mb-2 bg-light rounded">
                    <p>
                        <strong>These are subject to change.</strong> If you absolutely need an export or field added, please reach out on
                        <a href="{{ env('APP_DISCORD') }}" target="_blank" alt="Join the {{ env('APP_NAME') }} Discord Server" title="Join the {{ env('APP_NAME') }} Discord Server" class="">Discord</a>.
                    </p>
                    <ol class="no-bullet no-indent striped">
                        <li class="p-3 mb-3 rounded">
                            <!-- Wishlist -->
                            <h2>
                                <span class="fas fa-fw fa-scroll-old text-legendary"></span>
                                Wishlists
                            </h2>
                            Fields exported
                            <div class="bg-dark rounded p-1">
                                <code>
                                    {{ collect(App\Http\Controllers\ExportController::WISHLIST_A_HEADERS)->implode(', ') }}
                                </code>
                            </div>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.export.wishlist', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'type' => 'csv']) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-csv text-muted"></span>
                                        Download CSV
                                    </a>
                                </li>
                                <!-- <li class="list-inline-item">
                                    <a href="{{ route('guild.export.wishlist', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'type' => 'csv']) }}" target="_blank" class="tag">
                                        <span class="fas fa-fw fa-file-code text-muted"></span>
                                        Download JSON
                                    </a>
                                </li> -->
                            </ul>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
