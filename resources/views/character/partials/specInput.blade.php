@if (!isset($hideLabel) || !$hideLabel)
    <label for="spec" class="font-weight-bold">
        <span class="text-muted fas fa-fw fa-hat-wizard"></span>
        {{ __("Spec") }}
    </label>
@endif
<div class="form-group">
    <select name="{{ isset($name) && $name ? $name : 'spec' }}"
        class="js-spec form-control dark"
        data-index="{{ $index }}"
        {{ !$oldSpec ? 'disabled' : '' }}>
        <option value="" selected>
            {{ isset($hideLabel) && $hideLabel ? $hideLabel : '—' }}
        </option>
        @foreach (App\Character::specs($guild->expansion_id) as $key => $spec)
            @php
                $isOldSpec = $oldSpec == $key;
                if ($isOldSpec) {
                    $found = true;
                }
            @endphp
            <option value="{{ $key }}"
                data-class="{{ $spec['class'] }}"
                data-archetype="{{ $spec['archetype'] }}"
                data-icon="{{ $spec['icon'] }}"
                {{ $isOldSpec ? 'selected' : '' }}>
                {{ $spec['name'] }}
            </option>
        @endforeach
        @if (!$found && $oldSpec)
            <option value="{{ $oldSpec }}" selected>
                {{ $oldSpec }}
            </option>
        @endif
    </select>
    <input class="selectInput"" style="display:none;" />
</div>
