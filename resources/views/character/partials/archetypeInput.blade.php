@if (!isset($hideLabel) || !$hideLabel)
    <label for="archetype" class="font-weight-bold">
        {{ __("Role") }}
    </label>
@endif
<div class="form-group">
    <select name="{{ isset($name) && $name ? $name : 'archetype' }}"
        class="js-archetype form-control dark"
        data-index="{{ $index }}">
        <option value="" selected>
            {{ isset($hideLabel) && $hideLabel ? $hideLabel : '—' }}
        </option>
        @foreach (App\Character::archetypes() as $key => $archetype)
            <option value="{{ $key }}" {{ ($oldValue && $oldValue == $key) || ($character && $character->archetype == $key) ? 'selected' : '' }}>
                {{ $archetype }}
            </option>
        @endforeach
    </select>
</div>
