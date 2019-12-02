@extends('layouts.app')
@section('title', $user->username . "'s Profile - " . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>
                <span class="text-{{ strtolower($user->class) }}">
                    {{ $user->username }}
                </span>
                <small>
                    @if ($canEdit)
                        <small><a href="{{ route('showUser', ['id' => $user->id, 'username' => $user->username]) }}?edit=1">edit</a></small>
                    @endif
                    @if (Auth::user()->hasRole('admin|guild_master|officer'))
                        <small><a href="{{ route('banUser', ['id' => $user->id]) }}">{{ $user->banned_at ? 'unban' : 'ban' }}</a></small>
                    @endif
                </small>
            </h1>
            <small class="small text-muted">
                {{ $user->discord_username }}
            </small>
        </div>
        <div class="col-12 {{ $showPersonalNote ? 'col-md-6' : '' }}">
            <div class="row mb-3">
                <div class="col-12">
                    <span class="text-muted font-weight-bold">Roles</span>
                </div>
                <div class="col-12 m-2">
                    @if ($user->roles)
                        @foreach ($user->roles as $role)
                                <span class="tag" style="border-color:{{ $role->getColor() }};"><span class="role-circle" style="background-color:{{ $role->getColor() }}"></span>{{ $role->name }}</span>
                        @endforeach
                    @else
                        —
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <span class="text-muted font-weight-bold">Spec</span>
                </div>
                <div class="col-12">
                    {{ $user->spec ? $user->spec : '—' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <span class="text-muted font-weight-bold">Recipes</span>
                </div>
                <div class="col-12">
                    @if ($user->recipes)
                        <ul class="lesser-indent">
                            @foreach (explode("\n", $user->recipes) as $value)
                                <li class="js-markdown-inline">
                                    {{ $value }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        —
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <span class="text-muted font-weight-bold">Alts</span>
                </div>
                <div class="col-12">
                    @if ($user->alts)
                        <ul class="lesser-indent">
                            @foreach (explode("\n", $user->alts) as $value)
                                <li class="js-markdown-inline">
                                    {{ $value }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        —
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <span class="text-muted font-weight-bold">Rank</span>
                </div>
                <div class="col-12">
                    {{ $user->rank ? $user->rank : '—' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <span class="text-muted font-weight-bold">Rank Goal</span>
                </div>
                <div class="col-12">
                    {{ $user->rank_goal ? $user->rank_goal : '—' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <span class="text-muted font-weight-bold">Wishlist</span>
                </div>
                <div class="col-12">
                    @if ($user->wishlist)
                        <ul class="lesser-indent">
                            @foreach (explode("\n", $user->wishlist) as $value)
                                <li class="js-markdown-inline">
                                    {{ $value }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        —
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <span class="text-muted font-weight-bold">Loot Received</span>
                </div>
                <div class="col-12">
                    @if ($user->loot_received)
                        <ul class="lesser-indent">
                            @foreach (explode("\n", $user->loot_received) as $value)
                                <li class="js-markdown-inline">
                                    {{ $value }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        —
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <span class="text-muted font-weight-bold">Public Notes</span>
                </div>
                <div class="col-12">
                    {{ $user->note ? $user->note : '—' }}
                </div>
            </div>

            @if ($showOfficerNote)
                <div class="row mb-3">
                    <div class="col-12">
                        <span class="text-muted font-weight-bold">Officer Notes</span>
                    </div>
                    <div class="col-12">
                        {{ $user->officer_note ? $user->officer_note : '—' }}
                    </div>
                </div>
            @endif
        </div>
        @if ($showPersonalNote)
            <div class="col-12 col-md-6">
                <div class="row mb-3">
                    <div class="col-12">
                        <span class="text-muted font-weight-bold">Private Notes</span>
                        <a id="editPersonalNote" href="">edit</a>
                    </div>
                    <div id="personalNote" class="col-12" style="display:none;">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('updateUserPersonalNote', $user->id) }}">
                            {{ csrf_field() }}
                            <div class="mt-3">
                                <small class="text-muted">format text with <a target="_blank" href="{{ env('LINK_MARKDOWN') }}">markdown</a></small>
                            </div>
                            <div class="form-group">
                                <textarea name="personal_note" rows="20" maxlength="5000" placeholder="" class="form-control">{{ old('personal_note') ? old('personal_note') : $user->personal_note }}</textarea>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-success">Update</button>
                            </div>
                        </form>
                    </div>
                    <div class="js-markdown col-12">
                        {{ $user->personal_note ? $user->personal_note : '—' }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
    $("#editPersonalNote").click(function (e) {
        e.preventDefault();
        $("#personalNote").toggle();
    });
</script>
@endsection
