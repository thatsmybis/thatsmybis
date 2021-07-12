@foreach ($raids as $raid)
    <option value="{{ $raid->id }}"
        data-tokens="{{ $raid->id }}"
        data-name="{{ $raid->name }}"
        class="js-raid-option"
        hack="{{ $raid->id }}">
        {{ $raid->name }} @ {{ $raid->date }} {{ __("UTC") }}
    </option>
@endforeach
