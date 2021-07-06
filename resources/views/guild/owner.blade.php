@extends('layouts.app')
@section('title', __("Change Owner") . " - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-bold text-danger">
                        <span class="fas fa-fw fa-exclamation-triangle text-gold"></span>
                        {{ __("Change Guild Owner") }}
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
            <form class="form-horizontal" role="form" method="POST" action="{{ route('guild.submitOwner', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                <div class="row">
                    <div class="col-12 pt-2 pb-0 mb-3 bg-light rounded">

                        <ul class="no-bullet no-indent">
                            <li class="text-danger font-weight-bold">
                                {{ __("DANGER ZONE!") }}
                            </li>
                            <li class="text-danger font-weight-bold">
                                {{ __("You will lose ownership of the guild on this website") }}
                            </li>
                            <li class="">
                                {{ __("The user you select will be the new owner") }}
                            </li>
                            <li class="">
                                {{ __("You won't be able to undo this without their help") }}
                            </li>
                            <li class="">
                                {{ __("This will") }} <strong>{{ __("not") }}</strong> {{ __("affect your Discord server in any way") }}
                            </li>
                        </ul>

                        <div class="row">
                            <div class="col-12">
                                <label for="gm_role_id" class="font-weight-bold text-danger">
                                    <span class="fas fa-fw fa-crown text-gold"></span>
                                    {{ __("New Guild Owner") }}
                                </label>
                            </div>
                            <div class="col-md-6 col-sm-8 col-12">
                                <div class="form-group">
                                    <div class="form-group">
                                        <select name="member_id" class="selectpicker form-control dark" data-live-search="true" autocomplete="off">
                                            <option value="">
                                                —
                                            </option>
                                            @foreach ($members as $member)
                                                <option value="{{ $member->id }}"
                                                    data-tokens="{{ $member->id }}"
                                                    {{ Request::get('member_id') && Request::get('member_id') == $member->id ? 'selected' : ''}}>
                                                    {{ $member->username }}
                                                    ({{ $member->user->discord_username }})
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button class="btn btn-danger" onClick="return confirm('YOU WILL LOSE OWNERSHIP. Are you sure?');">
                        <span class="fas fa-fw fa-exclamation-triangle"></span>
                        {{ __("I want to lose ownership and give it to this member") }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
