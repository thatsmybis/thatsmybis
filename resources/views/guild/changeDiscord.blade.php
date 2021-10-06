@extends('layouts.app')
@section('title', __("Change Guild Discord") . " - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-bold text-danger">
                        <span class="fas fa-fw fa-exclamation-triangle text-gold"></span>
                        {{ __("Change Guild Discord Server") }}
                    </h1>
                </div>
            </div>
            @if (count($errors) > 0)
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            @endif
            <form class="form-horizontal" role="form" method="POST" action="{{ route('guild.submitChangeDiscord', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                <div class="row">
                    <div class="col-12 pt-2 pb-0 mb-3 bg-light rounded">

                        <span class="text-4 font-weight-bold">
                            {{ __("READ THE TEXT, OR POSSIBLY MESS STUFF UP.") }}
                        </span>

                        <ul class="no-bullet no-indent">
                            <li class="text-danger font-weight-bold">
                                {{ __("DANGER ZONE!") }}
                            </li>
                            <li class="text-danger font-weight-bold">
                                {{ __("THIS MAY BE IRREVERSIBLE") }}
                            </li>
                            <li class="text-warning">
                                {{ __("Members will no longer be able to visit this guild if they're not on the new server.") }}
                            </li>
                            <li class="">
                                {{ __("The current Discord server will be open for guild registration after you do this.") }}
                            </li>
                            <li class="">
                                {{ __("Only one guild per expansion can be registered to a Discord server.") }}
                            </li>
                            <li class="">
                                {{ __("So if the server gets taken, you won't be able to reverse this.") }}
                            </li>
                            <li class="">
                                {{ __("But you'll probably be the one to take it, so it's *probably* fine.") }}
                            </li>
                            <li class="mt-3">
                                {!! __("This will <strong>not</strong> affect your Discord server in any way.") !!}
                            </li>
                            <li class="mt-3 text-uncommon">
                                {{ __("If you came here hoping to ditch some test data, you're in the right spot!") }}
                            </li>
                            <li class="">
                                {{ __("Just choose a junk Discord server, then register a new guild on the good server.") }}
                            </li>
                            <li class="">
                                {{ __("You'll be back to square one with an empty guild.") }}
                            </li>
                        </ul>

                        <div class="row">
                            <div class="col-12">
                                {{ __("INSTRUCTIONS:") }}
                                <ol>
                                    <li>
                                        @include('guild/partials/addTheBot')
                                    </li>
                                    <li>
                                        {{ __("Choose a server below.") }}
                                    </li>
                                    <li>
                                        {{ __("Press the little submit button ofc.") }}
                                    </li>
                                </ol>
                            </div>
                            <div class="col-12">
                                <label for="gm_role_id" class="font-weight-bold text-danger">
                                    <span class="fas fa-fw fa-crown text-gold"></span>
                                    {{ __("Change Guild Discord Server") }}
                                </label>
                            </div>
                            <div class="col-md-6 col-sm-8 col-12">
                                @include('guild/partials/chooseDiscord')
                                <span class="text-warning">
                                    {{ __("I UNDERSTAND THAT I MIGHT NOT BE ABLE TO UNDO THIS IF AN ADMIN ON THE CURRENT DISCORD RE-REGISTERS A GUILD ON IT BEFORE I DO") }}
                                </span>
                                @include('guild/partials/iveAddedTheBot')
                            </div>
                        </div>
                    </div>
                </div>
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
