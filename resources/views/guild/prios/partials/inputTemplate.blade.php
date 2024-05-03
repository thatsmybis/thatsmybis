var inputTemplate =
    `<input type="checkbox" checked name="[character_id]" value="" style="display:none;">
    <input type="checkbox" checked name="[label]" value="" style="display:none;">

    <button type="button" class="js-input-button close close-top-right text-unselectable" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>

    <div class="js-sort-handle d-flex move-cursor text-unselectable mr-1 text-4">
        <div class="justify-content-center align-self-center">
            <span class="fas fa-fw fa-grip-vertical text-muted"></span>
        </div>
    </div>

    <div class="mt-2">
        <ul class="list-inline">
            <li class="list-inline-item">
                <div class="form-inline">
                    <div class="form-group">
                        <label class="sr-only" for="[order]">
                            {{ __("Rank") }}
                        </label>
                        &nbsp;
                        <input name="[order]"
                            type="number"
                            min="0"
                            max="{{ $maxPrios }}"
                            class="js-rank d-inline numbered form-control dark slim-order"
                            placeholder=""
                            autocomplete="off"
                            value="" />
                    </div>
                </div>
            </li>
            <li class="list-inline-item">
                <span class="js-input-label font-weight-medium"></span>
            </li>
            <li class="list-inline-item">
                <div class="checkbox">
                    <label class="small text-muted" for="[is_offspec]">
                        <input type="checkbox" name="[is_offspec]" value="1" class="" autocomplete="off">
                            {{ __("OS") }}
                    </label>
                </div>
            </li>
            <li class="list-inline-item">
                <div class="checkbox">
                    <label class="small text-muted" for="[is_received]">
                        <input type="checkbox" name="[is_received]" value="1" class="" autocomplete="off">
                            {{ __("Received") }}
                    </label>
                </div>
            </li>
            <li class="list-inline-item">
                <div class="checkbox">
                    <label class="small text-muted" for="[has_note]">
                        <input type="checkbox"
                            name="[has_note]"
                            value="1"
                            class="js-toggle-note"
                            data-index=""
                            autocomplete="off">
                            {{ __("Note") }}
                    </label>
                </div>
            </li>
        </ul>
        <div class="form-group mb-1" style="display:none;">
            <label for="[note]" class="sr-only font-weight-light">
                {{ __("Note") }}
            </label>
            <input type="text"
                class="js-note form-control dark slim"
                data-index=""
                placeholder="{{ __('add a note') }}"
                maxlength="140"
                name="[note]"
                value=""
                autocomplete="off">
        </div>
    </div>`;
