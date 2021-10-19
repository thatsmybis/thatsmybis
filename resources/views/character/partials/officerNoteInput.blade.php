@if (!isset($hideLabel) || !$hideLabel)
    <label for="officer_note" class="font-weight-bold">
        <span class="text-muted fas fa-fw fa-shield"></span>
        {{ __("Officer Note") }}
        <small class="text-muted">
            {{ __("only officers can see this") }}
        </small>
    </label>
@endif
@if (isStreamerMode())
    {{ __("Hidden in streamer mode") }}
@else
    <textarea maxlength="140" data-max-length="140" name="{{ isset($name) && $name ? $name : 'officer_note' }}" rows="1" placeholder="{{ isset($hideLabel) && $hideLabel ? $hideLabel : __('only officers can see this') }}" class="form-control dark">{{ $oldValue ? $oldValue : ($character ? $character->officer_note : '') }}</textarea>
@endif
