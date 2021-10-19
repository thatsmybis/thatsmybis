@if (!isset($hideLabel) || !$hideLabel)
    <label for="name" class="font-weight-bold">
        <span class="text-muted fas fa-fw fa-user"></span>
        {{ __("Character Name") }}
    </label>
@endif
<input name="{{ isset($name) && $name ? $name : 'name2' }}"
    class="js-name form-control dark"
    maxlength="40"
    type="text"
    placeholder="{{ __('eg. Radu') }}"
    value="{{ $oldValue ? $oldValue : ($character ? $character->name : '') }}" />
