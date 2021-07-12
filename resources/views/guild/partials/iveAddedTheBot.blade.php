<div class="form-group pt-3 pl-4">
    <div class="checkbox">
        <label>
            <input class="" type="checkbox" value="1" id="bot_added" onclick="toggleSubmit()" autocomplete="off">
            {{ __("I've added") }}
            <a href="https://discord.com/api/oauth2/authorize?client_id={{ env('DISCORD_KEY') }}&permissions=0&redirect_uri={{ env('DISCORD_REDIRECT_URI') }}&scope=bot"
                target="_blank">
                {{ __("the bot") }}
            </a>
            {{ __("to that server") }}
        </label>
    </div>
</div>

<div class="form-group pt-3">
    <button disabled class="btn btn-success" id="submit_button">
        <span class="fas fa-fw fa-check"></span>
        {{ __("A little submit button ofc") }}
    </button>
</div>
