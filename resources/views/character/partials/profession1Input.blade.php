@if (!isset($hideLabel) || !$hideLabel)
    <label for="class" class="font-weight-bold">
        <span class="text-muted fas fa-fw fa-flower-daffodil"></span>
        {{ __("Profession 1") }}
    </label>
@endif
<div class="form-group">
    <select name="{{ isset($name) && $name ? $name : 'profession_1' }}" class="form-control dark">
        <option value="" selected>
            {{ isset($hideLabel) && $hideLabel ? $hideLabel : '—' }}
        </option>

        @foreach (App\Character::professions($guild->expansion_id) as $key => $profession)
            <option value="{{ $key }}"
                {{ $oldValue ? ($oldValue == $key ? 'selected' : '') : ($character && $character->profession_1 == $key ? 'selected' : '') }}>
                {{ $profession }}
            </option>
        @endforeach
    </select>
</div>
