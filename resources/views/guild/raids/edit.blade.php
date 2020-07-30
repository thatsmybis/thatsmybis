@extends('layouts.app')
@section('title', (!$raid ? "Create" : "Edit") . " Raid Group - " . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-wight-medium">
                        <span class="fas fa-fw fa-helmet-battle text-dk"></span>
                        {{ $raid ? 'Edit' : 'Create' }} Raid Group
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

            <form class="form-horizontal" role="form" method="POST" action="{{ route(($raid ? 'guild.raid.update' : 'guild.raid.create'), ['guildSlug' => $guild->slug]) }}">
                {{ csrf_field() }}

                <input hidden name="id" value="{{ $raid ? $raid->id : '' }}" />

                <div class="row">
                    <div class="col-12 pt-2 pb-1 mb-3 bg-light rounded">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="name" class="font-weight-bold">
                                        <span class="fas fa-fw fa-users text-muted"></span>
                                        Raid Name
                                    </label>
                                    <input name="name"
                                        maxlength="255"
                                        type="text"
                                        class="form-control dark"
                                        placeholder="eg. Raid 1"
                                        value="{{ old('name') ? old('name') : ($raid ? $raid->name : '') }}" />
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 col-sm-8 col-12">
                                <div class="form-group">
                                    <label for="role_id" class="font-weight-bold">
                                        <span class="fab fa-fw fa-discord text-discord"></span>
                                        Discord Role
                                    </label>
                                    <small class="text-muted">
                                    </small>

                                    <div class="form-group">
                                        <select name="role_id" class="form-control dark">
                                            <option value="" selected>
                                                —
                                            </option>

                                            @foreach ($guild->roles as $role)
                                                <option value="{{ $role->id }}"
                                                    style="color:{{ $role->getColor() }};"
                                                    {{ old('role_id') ? (old('role_id') == $role->id ? 'selected' : '') : ($raid && $raid->role_id && $raid->role_id == $role->id ? 'selected' : '') }}>
                                                    {{ $role->name }}
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
                    <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
