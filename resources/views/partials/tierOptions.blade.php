@foreach ($tiers as $key => $tier)
    <option value="{{ $key }}"
        class="text-tier-{{ $key }}"
        hack="{{ $key }}">
        {{ $tierMode == App\Guild::TIER_MODE_NUM ? $key : $tier }}
    </option>
@endforeach
