<div class="form-group">
    <label for="discord_id" class="font-weight-bold">
        <span class="text-muted fab fa-fw fa-discord"></span>
        {{ __("Discord Server") }}
        <span class="text-muted font-weight-normal">
            {{ __("ones you have admin permissions on") }}
        </span>
    </label>
    <select name="discord_id_select" class="form-control">
        <option value="">
            —
        </option>

        @foreach ($guilds as $guild)
            <option value="{{ $guild['id'] }}"
                {{ $guild['registered'] ? 'disabled' : '' }}
                {{ old('discord_id_select') ? (old('discord_id_select') == $guild['id'] ? 'selected' : '') : '' }}>
                {{ $guild['registered'] ? '(already registered)' : '' }}
                {{ $guild['name'] }}
            </option>
        @endforeach
        @php
            // Destroy variable so it doesn't mess with other templates
            unset($guild);
        @endphp
    </select>
    <span class="text-muted cursor-pointer" id="discord_id_toggle"
        style="{{ old('discord_id') ? 'display:none;' : '' }}"
        onclick="$('#discord_id').show();$('#discord_id_toggle').hide();">
        <strong>{{ __("OR") }}</strong>
        {{ __("click here to manually enter a server ID") }}
    </span>
    <div class="" id="discord_id" style="{{ old('discord_id') ? '' : 'display:none;' }}">
        <label for="discord_id" class="font-weight-light">
            (optional)
            <span class="sr-only">
                {{ __("paste your server's ID") }}
            </span>
        </label>
        <span class="text-muted">
            <a href="https://support.discord.com/hc/en-us/articles/206346498-Where-can-I-find-my-User-Server-Message-ID-" target="_blank">
                {{ __("instructions") }}
            </a>
            {{ __("for finding a server ID") }}
        </span>
        <input name="discord_id" maxlength="255" type="text" class="form-control" placeholder="paste your guild's server ID" value="{{ old('discord_id') ? old('discord_id') : null }}" />
    </div>
</div>
