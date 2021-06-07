var inputTemplate =
    `<input type="checkbox" checked name="[character_id]" value="" style="display:none;">
    <input type="checkbox" checked name="[label]" value="" style="display:none;">

    <button type="button" class="js-input-button close close-top-right text-unselectable" aria-label="Close"><span aria-hidden="true" class="filter-button">&times;</span></button>

    <div class="js-sort-handle d-flex move-cursor text-unselectable mr-1 text-4">
        <div class="justify-content-center align-self-center">
            <span class="fas fa-fw fa-grip-vertical text-muted"></span>
        </div>
    </div>

    <div class="">
        <span class="js-input-label font-weight-medium">
        </span>
        <ul class="list-inline">
            <li class="list-inline-item">
                <div class="form-inline">
                    <div class="form-group">
                        <label class="sr-only" for="[order]">
                            Rank
                        </label>
                        &nbsp;
                        <input name="[order]"
                            type="number"
                            min="0"
                            max="{{ $maxPrios }}"
                            class="d-inline numbered form-control dark order"
                            placeholder=""
                            autocomplete="off"
                            value="" />
                    </div>
                </div>
            </li>
            <li class="list-inline-item">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="[is_offspec]" value="1" class="" autocomplete="off">
                            Offspec
                    </label>
                </div>
            </li>
            <li class="list-inline-item">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="[is_received]" value="1" class="" autocomplete="off">
                            Received
                    </label>
                </div>
            </li>
        </ul>
    </div>`;
