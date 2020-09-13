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





                        <div class="col-12 pt-2 pb-1 mb-3 bg-light rounded">
                            <div class="row">
                                <div class="col-1">
                                                    @if ($raid) 
                                                        <input type="checkbox" name="restrict_wish_prio_list" value="1" class="" onClick="JavaScript:updateWishListPrioRole(this,'restrictListsToRole');" autocomplete="off" {{ $raid->restrict_wish_prio_list_role ? 'checked' : '' }}> </div>
                                                    @else
                                                        <input type="checkbox" name="restrict_wish_prio_list" value="1" class="" onClick="JavaScript:updateWishListPrioRole(this,'restrictListsToRole');" autocomplete="off"> </div>
                                                    @endif
                                <div class="col-11">
                                <div class="form-group">
                                    <label for="name" class="ml-12">
                                        Restrict Access to Wish and Prio list 
                                    </label>
                                    
                                </div>
                            </div>
                        </div>


                        @if ($raid)
                            <div id="restrictListsToRole" class="row mb-3" style="visibility:  {{ $raid->restrict_wish_prio_list_role ? 'visible' : 'hidden' }}">
                        @else
                            <div id="restrictListsToRole" class="row mb-3" style="visibility: hidden">
                        @endif 

                            <div class="col-md-6 col-sm-8 col-12">
                                <div class="form-group">
                                    <label for="restrict_wish_prio_list_role" class="font-weight-bold">
                                        <span class="fab fa-fw fa-discord text-discord"></span>
                                        Restrict to Discord Role
                                    </label>
                                    <small class="text-muted">
                                    </small>

                                    <div class="form-group">
                                        <select name="restrict_wish_prio_list_role" class="form-control dark">
                                            <option id="noWishPrioList" value="" selected>
                                                —
                                            </option>

                                            @foreach ($guild->roles as $role)
                                                <option value="{{ $role->id }}"
                                                    style="color:{{ $role->getColor() }};"
                                                    {{ old('restrict_wish_prio_list_role') ? (old('restrict_wish_prio_list_role') == $role->id ? 'selected' : '') : ($raid && $raid->restrict_wish_prio_list_role && $raid->restrict_wish_prio_list_role == $role->id ? 'selected' : '') }}>
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

<script language="JavaScript">

            function updateWishListPrioRole(eventsender, idOfObjectToToggle){
                var newState = "hidden";
                if (eventsender.checked === true){
                    newState = "visible";
                } else {
                    document.getElementById("noWishPrioList").selected=true;
                }

                document.getElementById(idOfObjectToToggle).style.visibility = newState;

            }       
        </script>
@endsection
