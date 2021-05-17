@extends('layouts.app')
@section('title', "/gquit - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-bold text-danger">
                        <span class="fas fa-fw fa-exclamation-triangle text-gold"></span>
                        /gquit
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

            @if ($currentMember->user_id == $guild->user_id)
                <p>
                    You are the Guild Master. The Guild Master cannot quit the guild.
                </p>
                <p>
                    However you can go into the guild settings and transfer ownership, <em>then gquit</em>.
                </p>
                <p>
                    If you don't have anyone to transfer ownership to, you could disable the guild via the guild settings.
                </p>
                <p>
                    If you want to disband the guild, well... that feature probably doesn't exist yet. You could reach out on our Discord if you want help.
                </p>
            @else
                <form class="form-horizontal" role="form" method="POST" action="{{ route('member.submitGquit', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-12 pt-2 pb-0 mb-3 bg-light rounded">

                            <ul class="no-bullet no-indent">
                                <li class="text-danger font-weight-bold">
                                    DANGER ZONE!
                                </li>
                                <li class="text-3">
                                    You will leave
                                    <span class="text-{{ getExpansionColor($guild->expansion_id) }} font-weight-medium">
                                        &lt;{{ $guild->name }}&gt;
                                    </span>
                                </li>
                                <li class="text-3">
                                    You <strong>will not</strong> be able to rejoin without help
                                </li>
                                <li class="">
                                    To rejoin, an officer will need to unflag your profile as archived
                                </li>
                                <li class="">
                                    This <strong>will not</strong> affect your Discord or ingame status with
                                    <span class="text-{{ getExpansionColor($guild->expansion_id) }} font-weight-medium">
                                        &lt;{{ $guild->name }}&gt;
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-danger" onClick="return confirm('YOU CANNOT REJOIN WITHOUT AN OFFICER\'S HELP. Are you sure?');">
                            <span class="fas fa-fw fa-exclamation-triangle"></span>
                            /gquit
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
