@extends('layouts.app')
@section('title', "Edit " . $user->username . "'s Profile - " . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-xl-6 offset-xl-3 col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-12">
            <form class="form-horizontal" role="form" method="POST" action="{{ route('updateUser', $user->id) }}">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="username">
                        Name
                    </label>
                    <small class="text-muted">
                        Your main's name
                    </small>
                    <input name="username" maxlength="255" type="text" class="form-control" value="{{ old('username') ? old('username') : $user->username }}" />
                </div>

                <div class="form-group">
                    <label for="class">
                        Class
                    </label>
                    <small class="text-muted">
                        Your main's class
                    </small>
                    <select name="class" class="form-control">
                        <option value="" class="font-weight-strong">—</option>
                        <option value="druid" {{ old('class')   && old('class') == 'druid'   ? 'selected' : '' }} class="font-weight-strong">Druid</option>
                        <option value="hunter" {{ old('class')  && old('class') == 'hunter'  ? 'selected' : '' }} class="font-weight-strong">Hunter</option>
                        <option value="mage" {{ old('class')    && old('class') == 'mage'    ? 'selected' : '' }} class="font-weight-strong">Mage</option>
                        <option value="priest" {{ old('class')  && old('class') == 'priest'  ? 'selected' : '' }} class="font-weight-strong">Priest</option>
                        <option value="rogue" {{ old('class')   && old('class') == 'rogue'   ? 'selected' : '' }} class="font-weight-strong">Rogue</option>
                        <option value="shaman" {{ old('class')  && old('class') == 'shaman'  ? 'selected' : '' }} class="font-weight-strong">Shaman</option>
                        <option value="warlock" {{ old('class') && old('class') == 'warlock' ? 'selected' : '' }} class="font-weight-strong">Warlock</option>
                        <option value="warrior" {{ old('class') && old('class') == 'warrior' ? 'selected' : '' }} class="font-weight-strong">Warrior</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="spec">
                        Spec
                    </label>
                    <small class="text-muted">
                        Your main's spec
                    </small>
                    <input name="spec" maxlength="50" type="text" class="form-control" value="{{ old('spec') ? old('spec') : $user->spec }}" />
                </div>

                <div class="form-group">
                    <label for="professions">
                        Professions
                    </label>
                    <small class="text-muted">
                        Your main's professions
                    </small>
                    <textarea name="professions" maxlength="1000" rows="2" maxlength="" placeholder="" class="form-control">{{ old('professions') ? old('professions') : $user->professions }}</textarea>
                </div>

                <div class="form-group">
                    <label for="recipes">
                        Rare Recipes
                    </label>
                    <small class="text-muted">
                        Any rare recipes on your accounts
                    </small>
                    <textarea name="recipes" rows="3" maxlength="1000" placeholder="" class="form-control">{{ old('recipes') ? old('recipes') : $user->recipes }}</textarea>
                </div>

                <div class="form-group">
                    <label for="alts">
                        60 Alts
                    </label>
                    <small class="text-muted">
                        Only ones you can raid with
                    </small>
                    <textarea name="alts" rows="3" maxlength="300" placeholder="" class="form-control">{{ old('alts') ? old('alts') : $user->alts }}</textarea>
                </div>

                <div class="form-group">
                    <label for="rank">
                        PvP Rank
                    </label>
                    <small class="text-muted">
                        Current rank
                    </small>
                    <input name="rank" maxlength="50" type="text" class="form-control" value="{{ old('rank') ? old('rank') : $user->rank }}" />
                </div>

                <div class="form-group">
                    <label for="rank_goal">
                        PvP Rank Goal
                    </label>
                    <small class="text-muted">
                        Your expected rank
                    </small>
                    <input name="rank_goal" maxlength="50" type="text" class="form-control" value="{{ old('rank_goal') ? old('rank_goal') : $user->rank_goal }}" />
                </div>

                <div class="form-group">
                    <label for="wishlist">
                        Wishlist
                    </label>
                    <small class="text-muted">
                        Top 5 gear picks
                    </small>
                    <textarea name="wishlist" rows="3" maxlength="1000" placeholder="" class="form-control">{{ old('wishlist') ? old('wishlist') : $user->wishlist }}</textarea>
                </div>

                <div class="form-group">
                    <label for="loot_received">
                        Loot Received
                    </label>
                    <small class="text-muted">
                        Only loot you've received in guild runs
                    </small>
                    <textarea name="loot_received" rows="3" maxlength="1000" placeholder="" class="form-control">{{ old('loot_received') ? old('loot_received') : $user->loot_received }}</textarea>
                </div>

                <div class="form-group">
                    <label for="note">
                        Public Notes
                    </label>
                    <small class="text-muted">
                        ie. "Rushing for 8/8 tier 1"
                    </small>
                    <textarea name="note" rows="3" maxlength="1000" placeholder="" class="form-control">{{ old('note') ? old('note') : $user->note }}</textarea>
                </div>

                @if ($showOfficerNote)
                    <div class="form-group">
                        <label for="officer_note">
                            Officer Notes
                        </label>
                        <textarea name="officer_note" rows="3" maxlength="1000" placeholder="" class="form-control">{{ old('officer_note') ? old('officer_note') : $user->officer_note }}</textarea>
                    </div>
                @endif

                <div class="form-group">
                    <button class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
