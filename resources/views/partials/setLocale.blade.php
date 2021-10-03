<form id="localeForm" class="form-horizontal" role="form" method="POST" action="{{ route('setLocale') }}">
    {{ csrf_field() }}

    <div class="">
        <div class="form-group">
            <label for="locale" class="font-weight-bold">
                <span class="fas fa-fw fa-user-headset text-muted"></span>
                {{ __("Language") }}
                <small class="small text-muted">{{ __("some features are only in English") }}</small>
            </label>
            <select name="locale" class="form-control dark" data-live-search="false" autocomplete="off">
                <option value="">
                    —
                </option>
                @foreach (getLocales() as $key => $locale)
                    <option value="{{ $key }}" data-tokens="{{ $key }}" {{ isset($user) && $user && $user->locale == $key ? 'selected' : '' }}>
                        {{ $locale }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group">
        <ul class="list-inline">
            <li class="list-inline-item">
                <button class="btn btn-secondary"><span class="fas fa-fw fa-save"></span> {{ __("Save") }}</button>
            </li>
            <li class="list-inline-item">
                <a href="{{ route('translations') }}">{{ __("help with translations") }}</a>
            </li>
        </ul>
    </div>
</form>
