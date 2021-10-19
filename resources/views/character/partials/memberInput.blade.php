@if (!isset($hideLabel) || !$hideLabel)
    <label for="member_id" class="font-weight-normal">
        <span class="text-muted fas fa-fw fa-user"></span>
        {{ __("Guild Member") }}
    </label>
@endif
<div class="form-group">
    <select name="{{ isset($name) && $name ? $name : 'member_id' }}" class="form-control dark selectpicker" data-live-search="true">
        <option value="">
            {{ isset($hideLabel) && $hideLabel ? $hideLabel : '—' }}
        </option>

        @foreach ($guild->members as $member)
            <option value="{{ $member->id }}"
                data-tokens="{{ $member->id }}"
                {{ $oldValue ? ($oldValue == $member->id ? 'selected' : '') : ($character && $character->member_id == $member->id ? 'selected' : (isset($memberId) && $memberId == $member->id ? 'selected' : '')) }}>
                {{ $member->username }}
            </option>
        @endforeach
    </select>
</div>
