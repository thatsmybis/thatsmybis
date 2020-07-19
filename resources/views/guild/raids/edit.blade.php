@extends('layouts.app')
@section('title', (!$raid ? "Create" : "Edit") . " Raid - " . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-6 offset-xl-3 col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 mb-3">
                    <h4>{{ $raid ? 'Edit' : 'Create' }} Raid</h4>
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
            <form class="form-horizontal" role="form" method="POST" action="{{ route(($raid ? 'guild.raid.update' : 'guild.raid.create'), ['guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                <input hidden name="id" value="{{ $raid ? $raid->id : '' }}" />

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">
                                Raid Name
                            </label>
                            <input name="name"
                                maxlength="255"
                                type="text"
                                class="form-control"
                                placeholder="eg. Raid 1"
                                value="{{ old('name') ? old('name') : ($raid ? $raid->name : '') }}" />
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 col-sm-8 col-12">
                        <div class="form-group">
                            <label for="role_id" class="font-weight-bold">
                                Discord Role
                            </label>
                            <small class="text-muted">
                            </small>

                            <div class="form-group">
                                <select name="role_id" class="form-control">
                                    <option value="" selected>
                                        —
                                    </option>

                                    @foreach ($guild->roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ old('role_id') ? (old('role_id') == $role->id ? 'selected' : '') : ($raid && $raid->role_id && $raid->role_id == $role->id ? 'selected' : '') }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
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
