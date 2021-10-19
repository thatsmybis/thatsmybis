@if (!isset($hideLabel) || !$hideLabel)
    <label for="class" class="font-weight-bold">
        {{ __("Profession 2") }}
    </label>
@endif
<div class="form-group">
    <select name="{{ isset($name) && $name ? $name : 'profession_2' }}" class="form-control dark">
        <option value="" selected>
            {{ isset($hideLabel) && $hideLabel ? $hideLabel : '—' }}
        </option>

        @foreach (App\Character::professions($guild->expansion_id) as $key => $profession)
            <option value="{{ $key }}"
                {{ $oldValue ? ($oldValue == $key ? 'selected' : '') : ($character && $character->profession_2 == $key ? 'selected' : '') }}>
                {{ $profession }}
            </option>
        @endforeach
    </select>
</div>
