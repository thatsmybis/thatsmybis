@if (!isset($hideLabel) || !$hideLabel)
    <label for="rank" class="font-weight-bold">
        <span class="text-muted fas fa-fw fa-swords"></span>
        {{ __("PvP Rank") }}
    </label>
@endif
<input name="{{ isset($name) && $name ? $name : 'rank' }}"
    type="number"
    min="1"
    max="14"
    class="form-control dark"
    placeholder="{{ isset($hideLabel) && $hideLabel ? $hideLabel : '—' }}"
    value="{{ $oldValue ? $oldValue : ($character ? $character->rank : '') }}" />
