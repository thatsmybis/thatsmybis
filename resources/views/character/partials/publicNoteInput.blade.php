@if (!isset($hideLabel) || !$hideLabel)
    <label for="public_note" class="font-weight-bold">
        <span class="text-muted fas fa-fw fa-comment-alt-lines"></span>
        {{ __("Public Note") }}
        <small class="text-muted">
            {{ __("anyone in the guild can see this") }}
        </small>
    </label>
@endif
<textarea maxlength="140" data-max-length="140" name="{{ isset($name) && $name ? $name : 'public_note' }}" rows="1" placeholder="{{ isset($hideLabel) && $hideLabel ? $hideLabel : __('anyone in the guild can see this') }}" class="form-control dark">{{ $oldValue ? $oldValue : ($character ? $character->public_note : '') }}</textarea>
