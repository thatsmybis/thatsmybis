@if (!isset($hideLabel) || !$hideLabel)
    <label for="level" class="text-muted font-weight-normal">
        {{ __("Level") }}
    </label>
@endif
<input name="{{ isset($name) && $name ? $name : 'level' }}"
    type="number"
    min="1"
    max="{{ $guild->getMaxLevel() }}"
    class="form-control dark"
    placeholder="0"
    value="{{ $oldValue ? $oldValue : ($character ? $character->level : $guild->getMaxLevel()) }}" />
