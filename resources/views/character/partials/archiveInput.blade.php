<div class="checkbox">
    <label>
        <input type="checkbox" name="{{ isset($name) && $name ? $name : 'inactive_at' }}" value="1" class=""
            {{ $oldValue && $oldValue == 1 ? 'checked' : ($character->inactive_at ? 'checked' : '') }}>
            {{ __("Archive") }} <small class="text-muted">
                {{ __("no longer visible") }}
            </small>
    </label>
</div>
