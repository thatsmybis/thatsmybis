@if (!Auth::user())
    <p class="text-4 text-gold">
        {{ __("Try signing in!") }}
        {!! __("TMB completely changes <span class='font-weight-bold'>loot distribution</span>, <span class='font-weight-bold'>communication</span>, and <span class='font-weight-bold'>transparency</span>. Your guild won't look back.") !!}
    </p>
@endif
