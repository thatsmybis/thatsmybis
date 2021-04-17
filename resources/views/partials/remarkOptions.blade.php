@foreach (App\Raid::remarks() as $remark)
    <option value="{{ $remark }}"
        data-tokens="{{ $remark }}"
        hack="{{ $remark }}">
        {{ $remark }}
    </option>
@endforeach
