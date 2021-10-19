@if (!isset($hideLabel) || !$hideLabel)
    <label for="rank_goal" class="font-weight-bold">
        {{ __("PvP Rank Goal") }}
    </label>
@endif
<input name="{{ isset($name) && $name ? $name : 'rank_goal' }}"
    type="number"
    min="1"
    max="14"
    class="form-control dark"
    placeholder="{{ isset($hideLabel) && $hideLabel ? $hideLabel : '—' }}"
    value="{{ $oldValue ? $oldValue : ($character ? $character->rank_goal : '') }}" />
