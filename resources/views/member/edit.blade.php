@extends('layouts.app')
@section('title', "Edit Profile - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row mb-4">
                <div class="col-12 pt-2 bg-lightest rounded">
                    @include('member/partials/header', ['discordUsername' => $user->discord_username, 'showEdit' => false, 'titlePrefix' => 'Edit '])
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
            <form class="form-horizontal" role="form" method="POST" action="{{ route('member.update', ['guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                <input hidden name="id" value="{{ $member->id }}" />

                <div class="row mb-3 pb-1 pt-2 bg-light rounded">
                    <div class="col-sm-6 col-12">
                        <div class="form-group">
                            <label for="username" class="font-weight-bold">
                                <span class="text-muted fas fa-fw fa-user"></span>
                                Username
                            </label>
                            <input name="username"
                                maxlength="40"
                                type="text"
                                class="form-control"
                                placeholder="eg. Gurgthock"
                                value="{{ old('username') ? old('username') : $member->username }}" />
                        </div>
                    </div>
                </div>

                <div class="row mb-3 pb-1 pt-2 bg-light rounded">
                    <div class="col-12 mb-4">
                        <div class="form-group">
                            <label for="public_note" class="font-weight-bold">
                                <span class="text-muted fas fa-fw fa-comment-alt-lines"></span>
                                Public Note
                                <small class="text-muted">anyone in the guild can see this</small>
                            </label>
                            <textarea data-max-length="2000" name="public_note" rows="2" placeholder="anyone in the guild can see this" class="form-control">{{ old('public_note') ? old('public_note') : ($member ? $member->public_note : '') }}</textarea>
                        </div>
                    </div>

                    <!-- TODO: Permissions for who can see/set this -->
                    <div class="col-12 mb-4">
                        <div class="form-group">
                            <label for="officer_note" class="font-weight-bold">
                                <span class="text-muted fas fa-fw fa-shield"></span>
                                Officer Note
                                <small class="text-muted">only officers can see this</small>
                            </label>
                            <textarea data-max-length="2000" name="officer_note" rows="2" placeholder="only officers can see this" class="form-control">{{ old('officer_note') ? old('officer_note') : ($member ? $member->officer_note : '') }}</textarea>
                        </div>
                    </div>

                    <!-- TODO: Permissions for who can see/set this -->
                    <div class="col-12">
                        <div class="form-group">
                            <label for="personal_note" class="font-weight-bold">
                                <span class="text-muted fas fa-fw fa-lock"></span>
                                Personal Note
                                <small class="text-muted">only you can see this</small>
                            </label>
                            <textarea data-max-length="2000" name="personal_note" rows="2" placeholder="only you can see this" class="form-control">{{ old('personal_note') ? old('personal_note') : ($member ? $member->personal_note : '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
