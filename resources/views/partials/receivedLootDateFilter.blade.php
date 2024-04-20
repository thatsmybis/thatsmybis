<ul class="list-inline">
    <li class="list-inline-item">
        <form id="minReceivedLootDateForm"
            class="d-flex align-items-center"
            role="form"
            method="POST"
            autocomplete="off"
            action="{{ route('guild.item.updateDateFilter', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}"
        >
        {{ csrf_field() }}
            <div>
                <input name="min_date" min="2004-09-22"
                    max="{{ getDateTime('Y-m-d') }}"
                    value="{{ $minReceivedLootDate ? $minReceivedLootDate : ''}}"
                    type="date"
                    placeholder="—"
                    class="form-control dark"
                    autocomplete="off">
            </div>
            <div class="ml-1">
                <button id="submit" class="link"><span class="text-muted fas fa-fw fa-check"></span> {{ __('apply') }}</button>
            </div>
        </form>
    </li>
    <li class="list-inline-item">
        <form id="minReceivedLootDateForm"
            class="d-flex"
            role="form"
            method="POST"
            autocomplete="off"
            action="{{ route('guild.item.updateDateFilter', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}"
        >
            {{ csrf_field() }}
            <input name="min_date"
                value="{{ Illuminate\Support\Carbon::now()->subMonths(6)->format('Y-m-d') }}"
                type="date"
                class="d-none"
                autocomplete="off">
            <div>
                <button id="submit" class="link"><span class="text-muted fas fa-fw fa-sync"></span> {{ __('6 mo.') }}</button>
            </div>
        </form>

    </li>
    <li class="list-inline-item">
        <form id="minReceivedLootDateForm"
            class="d-flex"
            role="form"
            method="POST"
            autocomplete="off"
            action="{{ route('guild.item.updateDateFilter', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}"
        >
            {{ csrf_field() }}
            <input name="min_date"
                value="{{ Illuminate\Support\Carbon::now()->subMonths(3)->format('Y-m-d') }}"
                type="date"
                class="d-none"
                autocomplete="off">
            <div>
                <button id="submit" class="link"><span class="text-muted fas fa-fw fa-sync"></span> {{ __('3 mo.') }}</button>
            </div>
        </form>

    </li>
    <li class="list-inline-item">
        <form id="minReceivedLootDateForm"
            class="d-flex"
            role="form"
            method="POST"
            autocomplete="off"
            action="{{ route('guild.item.updateDateFilter', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}"
        >
            {{ csrf_field() }}
            <input name="min_date"
                value="{{ Illuminate\Support\Carbon::now()->subMonths(1)->format('Y-m-d') }}"
                type="date"
                class="d-none"
                autocomplete="off">
            <div>
                <button id="submit" class="link"><span class="text-muted fas fa-fw fa-sync"></span> {{ __('1 mo.') }}</button>
            </div>
        </form>

    </li>
</ul>
