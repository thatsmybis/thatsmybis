<div class="checkbox" style="margin-top:6px;">
    <label>
        <input type="checkbox" name="{{ isset($name) && $name ? $name : 'is_alt' }}" value="1" class=""
            {{ $oldValue && $oldValue == 1 ? 'checked' : ($character && $character->is_alt ? 'checked' : '') }}>
            {{ __("Alt Character") }} <small class="text-muted">
                {{-- __("will be tagged as an alt") --}}
            </small>
    </label>
</div>
