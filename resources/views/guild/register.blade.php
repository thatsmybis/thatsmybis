@extends('layouts.app')
@section('title', __('Register Guild') . ' - ' . config('app.name'))


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-8 col-xl-6 offset-lg-2 offset-xl-3">
            <h1>
                <span class="fas fa-fw fa-users-crown text-gold"></span>
                {{ __("Register a Guild") }}
            </h1>
        </div>
        <div class="col-12 col-lg-8 col-xl-6 offset-lg-2 offset-xl-3 mt-3 mb-3 pt-3 bg-lightest rounded">
            <p class="lead">{{ __("This website uses your guild's Discord server to manage your members' access and permissions.") }}</p>
            <p class="lead">{{ __("Instructions:") }}</p>
            <ol class="lead">
                <li>
                    @include('guild/partials/addTheBot')
                </li>
                <li>
                    {{ __("Fill out the form below.") }}
                </li>
            </ol>
            <p class="lead">{{ __("Once registered, invite your guild members by sharing the URL to your guild.") }}</p>
        </div>
        <div class="col-12 col-lg-8 col-xl-6 offset-lg-2 offset-xl-3 mb-3 pt-3 bg-lightest rounded">
            @if (count($errors) > 0)
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            @endif
            <form class="form-horizontal" role="form" method="POST" action="{{ route('guild.register') }}">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="name" class="font-weight-bold">
                        <span class="text-muted fas fa-fw fa-users"></span>
                        {{ __("Guild Name") }}
                    </label>
                    <input required name="name" maxlength="36" type="text" class="form-control" placeholder="must be unique" value="{{ old('name') ? old('name') : null }}" />
                </div>

                @include('guild/partials/chooseDiscord')

                <!-- Expansion -->
                <div class="form-group">
                    <label for="expansion_id" class="font-weight-bold">
                        <span class="text-muted fab fa-fw fa-battle-net"></span>
                        {{ __("Expansion") }}
                    </label>
                    <select name="expansion_id" class="form-control">
                        @foreach ($expansions as $expansion)
                            <option value="{{ $expansion->id }}"
                                {{ old('expansion_id') && old('expansion_id') == $expansion->id ? 'selected' : '' }}
                                {{ !$expansion->is_enabled ? 'disabled' : '' }}>
                                {{ $expansion->name_long }}
                                {{ !$expansion->is_enabled ? '(not yet supported)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @include('guild/partials/iveAddedTheBot')
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleSubmit() {
        var checkBox = document.getElementById("bot_added");
        var button = document.getElementById("submit_button");

        if (checkBox.checked == true) {
            button.disabled = false;
        } else {
            button.disabled = true;
        }
    }
    toggleSubmit();
</script>
@endsection
