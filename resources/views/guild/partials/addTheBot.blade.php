{{ __("Add") }}
<a href="https://discord.com/api/oauth2/authorize?client_id={{ env('DISCORD_KEY') }}&permissions=0&redirect_uri={{ env('DISCORD_REDIRECT_URI') }}&scope=bot"
    target="_blank" class="font-weight-bold">{{ __("this bot") }}</a>
{{ __("to your Discord server.") }}
<span class="text-muted">
    {{ __("(requires server admin or management permissions)") }}
</span>
