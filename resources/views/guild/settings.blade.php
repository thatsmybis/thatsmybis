@extends('layouts.app')
@section('title', "Guild Settings - " . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-6 offset-xl-3 col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 mb-3">
                    <h4>Guild Settings</h4>
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
            <form class="form-horizontal" role="form" method="POST" action="{{ route('guild.submitSettings', $guild->slug) }}">
                {{ csrf_field() }}

                <div class="row">
                    <div class="col-md-8 col-12">
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">
                                Guild Name
                            </label>
                            <input name="name" maxlength="255" type="text" class="form-control" placeholder="Must be unique" value="{{ old('name') ? old('name') : $guild->name }}" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 col-12">
                        <div class="form-group">
                            <label for="discord_id" class="font-weight-normal">
                                <span class="text-muted">Discord ID</span>
                                <small class="text-muted">
                                    locked
                                </small>
                            </label>
                            <input disabled
                                name="discord_id"
                                maxlength="255"
                                type="text"
                                class="form-control"
                                placeholder="Paste your guild's Discord ID here"
                                value="{{ old('discord_id') ? old('discord_id') : $guild->discord_id }}" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="calendar_link" class="font-weight-normal">
                                Google Calendar Link <small class="text-muted">optional</small>
                            </label>

                            <input
                                name="calendar_link"
                                maxlength="255"
                                type="text"
                                class="form-control"
                                placeholder="Paste the public URL"
                                value="{{ old('calendar_link') ? old('calendar_link') : $guild->calendar_link }}" />
                            <small class="text-muted">
                                Calendar settings > Integrate calendar > Public URL to this calendar
                            </small>
                        </div>
                    </div>
                </div>

                <hr class="light">

                <div class="row mt-4">
                    <div class="col-md-6 col-sm-8 col-12">
                        <div class="form-group">
                            <label for="member_roles" class="font-weight-bold">
                                Discord users with any of these roles are allowed to join
                            </label>
                            <small class="text-muted">
                            </small>

                            @php
                                $memberRoleIds = explode(',', $guild->member_role_ids);
                                $memberRoleLength = count($memberRoleIds) - 1;
                            @endphp

                            @for ($i = 0; $i < 12; $i++)
                                <div class="form-group {{ $i > 1 ? 'js-hide-empty' : '' }}" style="{{ $i > 1 ? 'display:none;' : '' }}">
                                    <select name="member_roles[]" class="form-control {{ $i > 0 ? 'js-show-next' : '' }}">
                                        <option value="" selected>
                                            —
                                        </option>

                                        @foreach ($guild->roles as $role)
                                            <option value="{{ $role->discord_id }}" {{ old('member_roles.' . $i) ? (old('member_roles.' . $i) == $role->discord_id ? 'selected' : '') : ($memberRoleLength >= $i && $memberRoleIds[$i] == $role->discord_id ? 'selected' : '') }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button class="btn btn-success">
                        <span class="fas fa-fw fa-save"></span>
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $(".js-show-next").change(function() {
            showNext(this);
        }).change();

        $(".js-show-next").keyup(function() {
            showNext(this);
        });
    });

    // If the current element has a value, show it and the next element that is hidden because it is empty
    function showNext(currentElement) {
        if ($(currentElement).val() != "") {
            $(currentElement).show();
            $(currentElement).parent().next(".js-hide-empty").show();
        }
    }
</script>
@endsection
