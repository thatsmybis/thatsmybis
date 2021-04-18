@foreach (App\Raid::remarks() as $key => $remark)
    <option value="{{ $key }}"
        data-tokens="{{ $key }}"
        hack="{{ $key }}">
        {{ $remark }}
    </option>
@endforeach
