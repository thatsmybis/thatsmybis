@if (!isset($hideLabel) || !$hideLabel)
    <label for="spec_label" class="font-weight-normal text-muted">
        {{ __("Spec Label") }}
        <span class="text-muted small">{{ __("optional") }}</span>
    </label>
@endif
@php
    $oldSpecLabel = $oldValue ? $oldValue : ($character ? $character->spec_label : '');
@endphp
<input name="{{ isset($name) && $name ? $name : 'spec_label' }}"
    class="js-spec-label form-control dark"
    data-index="{{ $index }}"
    {{ !$oldSpecLabel && !$oldSpec ? 'disabled' : '' }}
    maxlength="50"
    type="text"
    placeholder="{{ isset($hideLabel) && $hideLabel ? $hideLabel : __('eg. Boomkin') }}"
    value="{{ $oldSpecLabel }}" />
