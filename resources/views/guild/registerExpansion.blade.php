@extends('layouts.app')
@section('title', __('Add') . ' ' . $expansion->name_short . ' - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium">
                        <span class="fab fa-fw fa-battle-net text-mage"></span>
                        {{ __("Add") }} {{ $expansion->name_long }}
                    </h1>
                </div>

                <div class="col-12 pt-2 pb-0 mb-3 bg-light rounded">
                    @if (count($errors) > 0)
                        <ul class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <li>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <h2 class="font-weight-bold">
                        {{ __("Here's how this works:") }}
                    </h2>
                    <ol class="text-5">
                        <li>
                            {{ __("We'll give your guild a totally new space for") }} {{ $expansion->name_short }}.
                        </li>
                        <li>
                            {{ __("Your current setup and expansion will be untouched.") }}
                        </li>
                        <li>
                            {{ __("You'll manage the two expansions entirely separately.") }}
                        </li>
                    </ol>

                    <hr class="light mt-4">

                    <h2 class="font-weight-bold">
                        {{ __("FAQ") }}
                    </h2>

                    <span class="font-weight-bold font-italic text-5">
                        {{ __("Why use different spaces for each expansion?") }}
                    </span>
                    <p>
                        {{ __("Each expansion has different classes, professions, dungeons, and loot tables. It would be very complicated and messy to mix them.") }}
                    </p>

                    <span class="font-weight-bold font-italic text-5">
                        {{ __("Will my guild lose its current setup?") }}
                    </span>
                    <p>
                        <span class="font-weight-medium">No.</span>
                        {{ __("Your setup on the current expansion will not be affected in any way.") }}
                    </p>

                    <span class="font-weight-bold font-italic text-5">
                        {{ __("What data is copied over?") }}
                    </span>
                    <p>
                        <span class="font-weight-medium">None.</span>
                        {{ __("It's a fresh start for the new expansion.") }}
                    </p>

                    <span class="font-weight-bold font-italic text-5">
                        {{ __("How long will I have access to my old setup?") }}
                    </span>
                    <p>
                        <span class="font-weight-medium">Forever.</span>
                        {{ __("Whether you use it actively, or you just want to use it for reference.") }}
                    </p>

                    <span class="font-weight-bold font-italic text-5">
                        {{ __("How do I switch between expansions?") }}
                    </span>
                    <p>
                        {{ __("Via your") }} <a href="{{ route('home') }}">{{ __("dashboard") }}</a>.
                        {{ __("(click your guild name in the top left navbar) Or in guild settings.") }}
                    </p>

                    <div class="form-group pt-3 pl-4">
                        <label class="text-gold">
                            <input type="checkbox" value="1" id="i_get_it" autocomplete="off" onclick="toggleSubmit()">
                            {{ __("I get it, let me pass") }}
                        </label>
                    </div>

                    <div class="form-group pt-3">
                        <form class="form-horizontal"
                            role="form"
                            method="POST"
                            action="{{ route('guild.registerExpansion', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'expansionSlug' => $expansion->slug]) }}"
                        >
                            {{ csrf_field() }}
                            <button disabled class="btn btn-success" id="submit_button">
                                <span class="fas fa-fw fa-check"></span>
                                {{ __("Create new space for") }} {{ $expansion->name_short }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleSubmit() {
        var checkBox = document.getElementById("i_get_it");
        var button = document.getElementById("submit_button");

        if (checkBox.checked == true) {
            button.disabled = false;
        } else {
            button.disabled = true;
        }
    }
</script>
@endsection
