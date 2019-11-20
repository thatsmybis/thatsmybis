@extends('layouts.app')
@section('title', $user->username . "'s Profile - " . config('app.name'))

@section('content')

<div class="container-fluid p-5">
    <div class="col-xs-12">
        @if (!$canEdit)
            <h1>
                {{ $user->username }}

                    <br>
                    <small>
                        {{ $user->spec }} {{ $user->class }}
                    </small>
                @endif
            </h1>
        @else

        @endif


        <div class="form-group">
            <label for="username">
                Username
            </label>
            @if ($canEdit)
                <small class="text-muted">
                    Alchemy, Blacksmithing, Enchanting, Engineering, Herbalism, Leatherworking, Mining, Skinning, Tailoring
                </small>
                <textarea name="professions" rows="2" maxlength="" placeholder="" class="form-control">{{ old('professions') ? old('professions') : $user->professions }}</textarea>
            @else
                {{ $user->professions }}
            @endif
        </div>

        @if ($canEdit)
            <div class="form-group">
                <label for="class">
                    Class
                </label>
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
                <input name="spec" type="text" class="form-control" value="{{ old('spec') ? old('spec') : $user->spec }}" />
            </div>
        @endif

        <div class="form-group">
            <label for="professions">
                Professions
            </label>
            @if ($canEdit)
                <small class="text-muted">
                    Alchemy, Blacksmithing, Enchanting, Engineering, Herbalism, Leatherworking, Mining, Skinning, Tailoring
                </small>
                <textarea name="professions" rows="2" maxlength="" placeholder="" class="form-control">{{ old('professions') ? old('professions') : $user->professions }}</textarea>
            @else
                {{ $user->professions }}
            @endif
        </div>

        <div class="form-group">
            <label for="recipes">
                Recipes
            </label>
            @if ($canEdit)
                <textarea name="recipes" rows="3" maxlength="" placeholder="" class="form-control">{{ old('recipes') ? old('recipes') : $user->recipes }}</textarea>
            @else
                {{ $user->recipes }}
            @endif
        </div>

        <div class="form-group">
            <label for="alts">
                Alts
            </label>
            @if ($canEdit)
                <textarea name="alts" rows="3" maxlength="" placeholder="" class="form-control">{{ old('alts') ? old('alts') : $user->alts }}</textarea>
            @else
                {{ $user->alts }}
            @endif
        </div>

        <div class="form-group">
            <label for="rank">
                Rank
            </label>
            @if ($canEdit)
                <input name="rank" type="text" class="form-control" value="{{ old('rank') ? old('rank') : $user->rank }}" />
            @else
                {{ $user->rank }}
            @endif
        </div>

        <div class="form-group">
            <label for="rank_goal">
                Rank Goal
            </label>
            @if ($canEdit)
                <input name="rank_goal" type="text" class="form-control" value="{{ old('rank_goal') ? old('rank_goal') : $user->rank_goal }}" />
            @else
                {{ $user->rank_goal }}
            @endif
        </div>

        <div class="form-group">
            <label for="wishlist">
                Wishlist
            </label>
            @if ($canEdit)
                <textarea name="wishlist" rows="3" maxlength="" placeholder="" class="form-control">{{ old('wishlist') ? old('wishlist') : $user->wishlist }}</textarea>
            @else
                {{ $user->wishlist }}
            @endif
        </div>

        <div class="form-group">
            <label for="loot_received">
                Loot Received
            </label>
            @if ($canEdit)
                <textarea name="loot_received" rows="3" maxlength="" placeholder="" class="form-control">{{ old('loot_received') ? old('loot_received') : $user->loot_received }}</textarea>
            @else
                {{ $user->loot_received }}
            @endif
        </div>

        <div class="form-group">
            <label for="note">
                Notes
            </label>
            @if ($canEdit)
                <textarea name="note" rows="3" maxlength="" placeholder="" class="form-control">{{ old('note') ? old('note') : $user->note }}</textarea>
            @else
                {{ $user->note }}
            @endif
        </div>

        <div class="form-group">
            <label for="officer_note">
                Officer Notes
            </label>
            @if ($canEdit)
                <textarea name="officer_note" rows="3" maxlength="" placeholder="" class="form-control">{{ old('officer_note') ? old('officer_note') : $user->officer_note }}</textarea>
            @else
                {{ $user->officer_note }}
            @endif
        </div>
    </div>
form-groupiv>

@endsection
