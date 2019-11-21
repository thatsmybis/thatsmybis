@extends('layouts.app')
@section('title', "Edit " . $user->username . "'s Profile - " . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-xl-6 offset-xl-3 col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-12">
            @if (count($errors) > 0)
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="alert alert-danger">
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            @endif
            <form class="form-horizontal" role="form" method="POST" action="{{ route('updateUser', $user->id) }}">
                {{ csrf_field() }}
                <div class="form-group mt-5">
                    <label for="username" class="font-weight-bold">
                        Main's Name
                    </label>
                    <small class="text-muted">
                        Your main's name
                    </small>
                    <input name="username" maxlength="255" type="text" class="form-control" placeholder="What's your name?" value="{{ old('username') ? old('username') : $user->username }}" />
                </div>

                <div class="form-group mt-5">
                    <label for="class" class="font-weight-bold">
                        Class
                    </label>
                    <small class="text-muted">
                        Your main's class
                    </small>
                    <select name="class" class="form-control">
                        <option value="">—</option>
                        <option value="Druid" {{ old('class')   && old('class') == 'Druid'   ? 'selected' : ($user->class && $user->class == 'Druid'   ? 'selected' : '') }}>Druid</option>
                        <option value="Hunter" {{ old('class')  && old('class') == 'Hunter'  ? 'selected' : ($user->class && $user->class == 'Hunter'  ? 'selected' : '') }}>Hunter</option>
                        <option value="Mage" {{ old('class')    && old('class') == 'Mage'    ? 'selected' : ($user->class && $user->class == 'Mage'    ? 'selected' : '') }}>Mage</option>
                        <option value="Priest" {{ old('class')  && old('class') == 'Priest'  ? 'selected' : ($user->class && $user->class == 'Priest'  ? 'selected' : '') }}>Priest</option>
                        <option value="Rogue" {{ old('class')   && old('class') == 'Rogue'   ? 'selected' : ($user->class && $user->class == 'Rogue'   ? 'selected' : '') }}>Rogue</option>
                        <option value="Shaman" {{ old('class')  && old('class') == 'Shaman'  ? 'selected' : ($user->class && $user->class == 'Shaman'  ? 'selected' : '') }}>Shaman</option>
                        <option value="Warlock" {{ old('class') && old('class') == 'Warlock' ? 'selected' : ($user->class && $user->class == 'Warlock' ? 'selected' : '') }}>Warlock</option>
                        <option value="Warrior" {{ old('class') && old('class') == 'Warrior' ? 'selected' : ($user->class && $user->class == 'Warrior' ? 'selected' : '') }}>Warrior</option>
                    </select>
                </div>

                <div class="form-group mt-5">
                    <label for="spec" class="font-weight-bold">
                        Spec
                    </label>
                    <small class="text-muted">
                        Your main's spec
                    </small>
                    <input name="spec" maxlength="50" type="text" class="form-control" placeholder="eg. Boomkin" value="{{ old('spec') ? old('spec') : $user->spec }}" />
                </div>

                <div class="form-group mt-5">
                    <label for="professions" class="font-weight-bold">
                        Professions
                    </label>
                    <small class="text-muted">
                        SELECT ALL THAT APPLY
                    </small>

                    <?php
                    $professionsArray = splitByLine($user->professions);
                    $professionsLength = count($professionsArray) - 1;
                    ?>

                    <select name="professions[]" class="form-control" size="16" multiple>
                        <option value="Alchemy"                      {{ old('professions') && old('professions') == "Alchemy"                     ? 'selected' : (in_array("Alchemy"                     , $professionsArray) ? 'selected' : '') }}>Alchemy</option>
                        <option value="Blacksmithing"                {{ old('professions') && old('professions') == "Blacksmithing"               ? 'selected' : (in_array("Blacksmithing"               , $professionsArray) ? 'selected' : '') }}>Blacksmithing</option>
                        <option value="Blacksmithing - Armorsmith"   {{ old('professions') && old('professions') == "Blacksmithing - Armorsmith"  ? 'selected' : (in_array("Blacksmithing - Armorsmith"  , $professionsArray) ? 'selected' : '') }}>Blacksmithing - Armorsmith</option>
                        <option value="Blacksmithing - Weaponsmith"  {{ old('professions') && old('professions') == "Blacksmithing - Weaponsmith" ? 'selected' : (in_array("Blacksmithing - Weaponsmith" , $professionsArray) ? 'selected' : '') }}>Blacksmithing - Weaponsmith</option>
                        <option value="Enchanting"                   {{ old('professions') && old('professions') == "Enchanting"                  ? 'selected' : (in_array("Enchanting"                  , $professionsArray) ? 'selected' : '') }}>Enchanting</option>
                        <option value="Engineering"                  {{ old('professions') && old('professions') == "Engineering"                 ? 'selected' : (in_array("Engineering"                 , $professionsArray) ? 'selected' : '') }}>Engineering</option>
                        <option value="Engineering - Gnome"          {{ old('professions') && old('professions') == "Engineering - Gnome"         ? 'selected' : (in_array("Engineering - Gnome"         , $professionsArray) ? 'selected' : '') }}>Engineering - Gnome</option>
                        <option value="Engineering - Goblin"         {{ old('professions') && old('professions') == "Engineering - Goblin"        ? 'selected' : (in_array("Engineering - Goblin"        , $professionsArray) ? 'selected' : '') }}>Engineering - Goblin</option>
                        <option value="Herbalism"                    {{ old('professions') && old('professions') == "Herbalism"                   ? 'selected' : (in_array("Herbalism"                   , $professionsArray) ? 'selected' : '') }}>Herbalism</option>
                        <option value="Leatherworking"               {{ old('professions') && old('professions') == "Leatherworking"              ? 'selected' : (in_array("Leatherworking"              , $professionsArray) ? 'selected' : '') }}>Leatherworking</option>
                        <option value="Leatherworking - Elemental"   {{ old('professions') && old('professions') == "Leatherworking - Elemental"  ? 'selected' : (in_array("Leatherworking - Elemental"  , $professionsArray) ? 'selected' : '') }}>Leatherworking - Elemental</option>
                        <option value="Leatherworking - Dragonscale" {{ old('professions') && old('professions') == "Leatherworking - Dragonscale"? 'selected' : (in_array("Leatherworking - Dragonscale", $professionsArray) ? 'selected' : '') }}>Leatherworking - Dragonscale</option>
                        <option value="Leatherworking - Tribal"      {{ old('professions') && old('professions') == "Leatherworking - Tribal"     ? 'selected' : (in_array("Leatherworking - Tribal"     , $professionsArray) ? 'selected' : '') }}>Leatherworking - Tribal</option>
                        <option value="Mining"                       {{ old('professions') && old('professions') == "Mining"                      ? 'selected' : (in_array("Mining"                      , $professionsArray) ? 'selected' : '') }}>Mining</option>
                        <option value="Skinning"                     {{ old('professions') && old('professions') == "Skinning"                    ? 'selected' : (in_array("Skinning"                    , $professionsArray) ? 'selected' : '') }}>Skinning</option>
                        <option value="Tailoring"                    {{ old('professions') && old('professions') == "Tailoring"                   ? 'selected' : (in_array("Tailoring"                   , $professionsArray) ? 'selected' : '') }}>Tailoring</option>
                    </select>
                </div>

                <div class="form-group mt-5">
                    <label for="recipes" class="font-weight-bold">
                        Rare Recipes
                    </label>
                    <small class="text-muted">
                        Any rare recipes on your accounts
                    </small>
                    <?php
                    $recipesArray = splitByLine($user->recipes);
                    $recipesLength = count($recipesArray) - 1;
                    ?>
                    <div class="form-group">
                        <input name="recipes[]" maxlength="100" type="text" class="form-control" placeholder="eg. Dirge's Kickin' Chimaerok Chops" value="{{ old('recipes.0]') ? old('recipes.0') : ($recipesLength >= 0 ? $recipesArray[0] : '') }}" />
                    </div>
                    <div class="form-group">
                        <input name="recipes[]" maxlength="100" type="text" class="js-show-next form-control" placeholder="eg. Flask of Distilled Wisdom"       value="{{ old('recipes.1') ? old('recipes.1') : ($recipesLength >= 1 ? $recipesArray[1] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="recipes[]" maxlength="100" type="text" class="js-show-next form-control" placeholder="eg. Enchant Weapon - Crusader"       value="{{ old('recipes.2') ? old('recipes.2') : ($recipesLength >= 2 ? $recipesArray[2] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="recipes[]" maxlength="100" type="text" class="js-show-next form-control" placeholder="eg. Enchant Weapon - Spell Power"    value="{{ old('recipes.3') ? old('recipes.3') : ($recipesLength >= 3 ? $recipesArray[3] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="recipes[]" maxlength="100" type="text" class="js-show-next form-control" placeholder="eg. Lionheart Helm"                  value="{{ old('recipes.4') ? old('recipes.4') : ($recipesLength >= 4 ? $recipesArray[4] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="recipes[]" maxlength="100" type="text" class="js-show-next form-control" placeholder="eg. Arcanite Reaper"                 value="{{ old('recipes.5') ? old('recipes.5') : ($recipesLength >= 5 ? $recipesArray[5] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="recipes[]" maxlength="100" type="text" class="js-show-next form-control" placeholder="eg. Devilsaur Leggings"              value="{{ old('recipes.6') ? old('recipes.6') : ($recipesLength >= 6 ? $recipesArray[6] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="recipes[]" maxlength="100" type="text" class="js-show-next form-control" placeholder="eg. Hide of the Wild"                value="{{ old('recipes.7') ? old('recipes.7') : ($recipesLength >= 7 ? $recipesArray[7] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="recipes[]" maxlength="100" type="text" class="js-show-next form-control" placeholder="eg. Bottomless Bag"                  value="{{ old('recipes.8') ? old('recipes.8') : ($recipesLength >= 8 ? $recipesArray[8] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="recipes[]" maxlength="100" type="text" class="js-show-next form-control" placeholder="eg. Force Reactive Disk"             value="{{ old('recipes.9') ? old('recipes.9') : ($recipesLength >= 9 ? $recipesArray[9] : '') }}" />
                    </div>
                </div>

                <div class="form-group mt-5">
                    <label for="alts" class="font-weight-bold">
                        60 Alts
                    </label>
                    <small class="text-muted">
                        Only ones you can raid with
                    </small>

                    <?php
                    $altsArray = splitByLine($user->alts);
                    $altsLength = count($altsArray) - 1;
                    ?>

                    <div class="form-group">
                        <input name="alts[]" maxlength="50" type="text" class="form-control" placeholder="eg. 60 Undead Rogue"   value="{{ old('alts.0') ? old('alts.0') : ($altsLength >= 0 ? $altsArray[0] : '') }}" />
                    </div>
                    <div class="form-group">
                        <input name="alts[]" maxlength="50" type="text" class="js-show-next form-control" placeholder="eg. 60 Orc Hunter"     value="{{ old('alts.1') ? old('alts.1') : ($altsLength >= 1 ? $altsArray[1] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="alts[]" maxlength="50" type="text" class="js-show-next form-control" placeholder="eg. 60 Undead Mage"    value="{{ old('alts.2') ? old('alts.2') : ($altsLength >= 2 ? $altsArray[2] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="alts[]" maxlength="50" type="text" class="js-show-next form-control" placeholder="eg. 60 Tauren Druid"   value="{{ old('alts.3') ? old('alts.3') : ($altsLength >= 3 ? $altsArray[3] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="alts[]" maxlength="50" type="text" class="js-show-next form-control" placeholder="eg. 60 Troll Priest"   value="{{ old('alts.4') ? old('alts.4') : ($altsLength >= 4 ? $altsArray[4] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="alts[]" maxlength="50" type="text" class="js-show-next form-control" placeholder="eg. 60 Orc Warrior"    value="{{ old('alts.5') ? old('alts.5') : ($altsLength >= 5 ? $altsArray[5] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="alts[]" maxlength="50" type="text" class="js-show-next form-control" placeholder="eg. 60 Undead Warlock" value="{{ old('alts.6') ? old('alts.6') : ($altsLength >= 6 ? $altsArray[6] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="alts[]" maxlength="50" type="text" class="js-show-next form-control" placeholder="eg. 60 Tauren Shaman"  value="{{ old('alts.7') ? old('alts.7') : ($altsLength >= 7 ? $altsArray[7] : '') }}" />
                    </div>
                </div>

                <div class="form-group mt-5">
                    <label for="rank" class="font-weight-bold">
                        PvP Rank
                    </label>
                    <small class="text-muted">
                        Current rank
                    </small>
                    <input name="rank" maxlength="50" type="text" class="form-control" placeholder="eg. 2" value="{{ old('rank') ? old('rank') : $user->rank }}" />
                </div>

                <div class="form-group mt-5">
                    <label for="rank_goal" class="font-weight-bold">
                        PvP Rank Goal
                    </label>
                    <small class="text-muted">
                        Your expected rank
                    </small>
                    <input name="rank_goal" maxlength="50" type="text" class="form-control" placeholder="eg. 14" value="{{ old('rank_goal') ? old('rank_goal') : $user->rank_goal }}" />
                </div>

                <div class="form-group mt-5">
                    <label for="raid_group" class="font-weight-bold">
                        Raid Group
                    </label>
                    <small class="text-muted">
                        Only select one if you have one
                    </small>

                    <select name="raid_group" class="form-control">
                        <option value="">—</option>
                        <option value="Mythic" {{ old('raid_group')  && old('raid_group') == 'mythic'  ? 'selected' : ($user->raid_group && $user->raid_group == 'Mythic' ? 'selected' : '') }}>Mythic</option>
                        <option value="Night" {{ old('raid_group')   && old('raid_group') == 'night'   ? 'selected' : ($user->raid_group && $user->raid_group == 'Night' ? 'selected' : '') }}>Night</option>
                        <option value="Weekend" {{ old('raid_group') && old('raid_group') == 'weekend' ? 'selected' : ($user->raid_group && $user->raid_group == 'Weekend' ? 'selected' : '') }}>Weekend</option>
                    </select>
                </div>

                <div class="form-group mt-5">
                    <label for="wishlist" class="font-weight-bold">
                        Raid Gear Wishlist
                    </label>
                    <small class="text-muted">
                        CURRENT CONTENT ONLY
                    </small>

                    <?php
                    $wishlistArray = splitByLine($user->wishlist);
                    $wishlistLength = count($wishlistArray) - 1;
                    ?>

                    <div class="form-group">
                        <input name="wishlist[]" maxlength="200" type="text" class="form-control" placeholder="eg. Shadowstrike"                value="{{ old('wishlist.0') ? old('wishlist.0') : ($wishlistLength  >= 0 ? $wishlistArray[0] : '') }}" />
                    </div>
                    <div class="form-group">
                        <input name="wishlist[]" maxlength="200" type="text" class="js-show-next form-control" placeholder="eg. Obsidian Edged Blade"        value="{{ old('wishlist.1') ? old('wishlist.1') : ($wishlistLength  >= 1 ? $wishlistArray[1] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="wishlist[]" maxlength="200" type="text" class="js-show-next form-control" placeholder="eg. Talisman of Ephemeral Power" value="{{ old('wishlist.2') ? old('wishlist.2') : ($wishlistLength  >= 2 ? $wishlistArray[2] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="wishlist[]" maxlength="200" type="text" class="js-show-next form-control" placeholder="eg. Gianstalker's Leggings"      value="{{ old('wishlist.3') ? old('wishlist.3') : ($wishlistLength  >= 3 ? $wishlistArray[3] : '') }}" />
                    </div>
                    <div class="js-hide-empty form-group" style="display:none;">
                        <input name="wishlist[]" maxlength="200" type="text" class="js-show-next form-control" placeholder="eg. Striker's Mark"              value="{{ old('wishlist.4') ? old('wishlist.4') : ($wishlistLength  >= 4 ? $wishlistArray[4] : '') }}" />
                    </div>
                </div>

                <div class="form-group mt-5">
                    <label for="loot_received" class="font-weight-bold">
                        Loot Received
                    </label>
                    <small class="text-muted">
                        Only loot you've received in guild runs
                    </small>

                    <?php
                    $lootArray = splitByLine($user->loot_received);
                    $lootLength = count($lootArray) - 1;
                    ?>

                    @for ($i = 0; $i < 30; $i++)
                        <div class="form-group {{ $i > 1 ? 'js-hide-empty' : '' }}" style="{{ $i > 1 ? 'display:none;' : '' }}">
                            <input name="loot_received[]" maxlength="200" type="text" class="{{ $i > 0 ? 'js-show-next' : '' }} form-control" placeholder="eg. Core Hound Tooth" value="{{ old('loot_received.' . $i) ? old('loot_received.' . $i) : ($lootLength  >= $i ? $lootArray[$i] : '') }}" />
                        </div>
                    @endfor
                </div>

                <div class="form-group mt-5">
                    <label for="note" class="font-weight-bold">
                        Public Notes
                    </label>
                    <small class="text-muted">
                        ie. "Rushing for 8/8 tier 1"
                    </small>
                    <textarea name="note" rows="3" maxlength="1000" placeholder="" class="form-control">{{ old('note') ? old('note') : $user->note }}</textarea>
                </div>

                @if ($showOfficerNote)
                    <div class="form-group">
                        <label for="officer_note" class="font-weight-bold">
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
