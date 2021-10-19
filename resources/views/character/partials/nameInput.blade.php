@if (!isset($hideLabel) || !$hideLabel)
    <label for="name" class="font-weight-bold">
        <span class="text-muted fas fa-fw fa-user"></span>
        {{ __("Character Name") }}
    </label>
@endif
<input name="{{ isset($name) && $name ? $name : 'name' }}"
    maxlength="40"
    type="text"
    class="form-control dark"
    placeholder="eg. Radu"
    value="{{ $oldValue ? $oldValue : ($character ? $character->name : '') }}" />
