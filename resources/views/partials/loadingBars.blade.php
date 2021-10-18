<div id="{{ isset($loadingBarId) ? $loadingBarId : '' }}" class="loadingBarContainer d-flex justify-content-center">
    <div class="flex-column">
        <div class="loadingBars">
            <div class="bg-expansion-{{ isset($guild->expansion_id) ? $guild->expansion_id : (isset($expansionId) ? $expansionId : null) }}"></div>
            <div class="bg-expansion-{{ isset($guild->expansion_id) ? $guild->expansion_id : (isset($expansionId) ? $expansionId : null) }}"></div>
            <div class="bg-expansion-{{ isset($guild->expansion_id) ? $guild->expansion_id : (isset($expansionId) ? $expansionId : null) }}"></div>
        </div>
        <div class="text-center text-expansion-{{ isset($guild->expansion_id) ? $guild->expansion_id : (isset($expansionId) ? $expansionId : null) }}">
            {{ __("Loading...") }}
        </div>
    </div>
</div>
