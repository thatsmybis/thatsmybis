@if (!isset($hideLabel) || !$hideLabel)
    <label for="class" class="font-weight-bold">
        <span class="text-muted fas fa-fw fa-swords"></span>
        {{ __("Class") }}
    </label>
@endif
<div class="form-group">
    <select name="{{ isset($name) && $name ? $name : 'class' }}"
        class="js-class form-control dark"
        data-index="{{ $index }}">
        <option value="">
            {{ isset($hideLabel) && $hideLabel ? $hideLabel : '—' }}
        </option>

        @foreach (App\Character::classes($guild->expansion_id) as $key => $class)
            <option value="{{ $key }}" class="text-{{ strtolower($key) }}-important"
                {{ $oldValue ? ($oldValue == $key ? 'selected' : '') : ($character && $character->class == $key ? 'selected' : '') }}>
                {{ $class }}
            </option>
        @endforeach
    </select>
</div>
