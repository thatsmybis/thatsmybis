@extends('layouts.app')
@section('title', __("Export") . " - " . config('app.name'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="row">
                    <div class="col-12 pt-2 mb-2">
                        <h1 class="font-weight-medium">
                            <span class="fas fa-fw fa-file-export text-gold"></span>
                            {{ __("Gargul Export") }}
                        </h1>
                        <small>
                            <strong>{{ __("Note") }}:</strong> {{ __("Press the 'Click to copy' button and paste in-game ( /gl tmb )") }}
                        </small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-2 col-md-3 col-6">
                        <div class="form-group">
                            <label for="gargul_min_date" class="text-muted font-weight-light">
                                <span class="fas fa-fw fa-calendar-minus text-muted"></span>
                                {{ __("Min Date") }}
                            </label>
                            <input name="gargul_min_date"
                                id="gargul_min_date"
                                min="2004-09-22"
                                max="{{ getDateTime('Y-m-d') }}"
                                value=""
                                type="date"
                                placeholder="—"
                                class="form-control dark text-muted"
                                autocomplete="off">
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-3 col-6">
                        <div class="form-group">
                            <label for="gargul_max_date" class="text-muted font-weight-light">
                                <span class="fas fa-fw fa-calendar-plus text-muted"></span>
                                {{ __("Max Date") }}
                            </label>
                            <input name="gargul_max_date"
                                id="gargul_max_date"
                                min="2004-09-22"
                                value=""
                                max="{{ getDateTime('Y-m-d') }}"
                                type="date"
                                placeholder="—"
                                class="form-control dark text-muted"
                                autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="row mt-4 mb-4">
                    <div class="col-12 pt-2 pb-2 bg-light rounded">
                        <div class="row">
                            <div class="col-12 mt-3">
                                <div class="form-group" style="float: left;">
                                    <button type="button" class="js-toggle-import btn btn-primary" data-copy-segment="exportData">
                                        <span class="fas fa-file-code"></span>
                                        {{ __("Click to copy") }}
                                    </button>
                                </div>
                                <div class="dropdown mt-2 ml-4" style="float: left;">
                                    <a class="dropdown-toggle font-weight-bold text-legendary" id="wishlistDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="fas fa-fw fa-scroll-old"></span>
                                        {{ __("Wishlists") }}
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="wishlistDropdown">
                                        @for ($i = 1; $i <= $maxWishlistLists; $i++)
                                            <a class="dropdown-item"
                                               href="javascript: void(0);">
                                                <input type="checkbox" name="gargul_wishlist[]" id="gargul_wishlist_{{ $i }}" value="{{ $i }}" {{ $guild->current_wishlist_number == $i ? 'checked="checked"' : '' }}>
                                                <label for="gargul_wishlist_{{ $i }}">
                                                    {{ __("Wishlist") }} {{ $i }}
                                                    @if ($guild->current_wishlist_number == $i)
                                                        <span class="text-success">{{ __('(active)') }}</span>
                                                    @else
                                                        <span class="text-danger">{{ __('(inactive)') }}</span>
                                                    @endif
                                                </label>
                                            </a>
                                        @endfor
                                    </div>
                                </div>
                                <div style="float: left;" class="mt-2 ml-4">
                                    <a href="{{ route('guild.export.gargul', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raw' => true, 'gargul_wishlist' => request()->get('gargul_wishlist')]) }}" target="_blank">
                                        <span class="fas fa-fw fas fa-file-code"></span>
                                        {{ __("Raw") }}
                                    </a>
                                </div>
                            </div>
                            <div class="col-12">
                                <div id="input-string" class="form-group">
                                    <textarea disabled="disabled" id="exportData" name="import_textarea" rows="20" class="form-control dark">{{ $data }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Page may be loaded with min and max date arguments. We want to preserve them by
    // Adding them to the min and max date inputs. But first we need to convert from
    // PHP's UTC timestamp to a Javascript local timestamp.
    // Add 000 to date for the milliseconds Javascript wants for timestamps.
    let minDate = {{ Request::get('gargul_min_date') ? Request::get('gargul_min_date') . '000' : 'null'}};
    minDate = minDate ? moment.utc(minDate).format('Y-MM-DD') : '';
    let maxDate = {{ Request::get('gargul_max_date') ? Request::get('gargul_max_date') . '000' : 'null'}};
    maxDate = maxDate ? moment.utc(maxDate).format('Y-MM-DD') : '';

    $(document).ready(function() {
        // Load dates into inputs on first load.
        $("#gargul_min_date").val(minDate);
        $("#gargul_max_date").val(maxDate);

        let checkboxSelector = "input[type='checkbox'][name='gargul_wishlist[]']";

        // Make sure the proper wishlists are checked based on the current URI
        (function () {
            let currentURI = document.location.href;

            if (currentURI.indexOf("gargul_wishlist") === -1) {
                return;
            }

            let queryStart = currentURI.lastIndexOf('?');
            let wishlists = currentURI.substr(queryStart + 1).replace(/gargul_wishlist\[\]=/g,'').split('&');

            $(checkboxSelector).prop('checked', function(){
                return $.inArray(this.value, wishlists) !== -1;
            });
        })();

        // Update the page when the user selects different wishlists
        $(checkboxSelector).change(refreshExport);
        // Update the page when the user selects different dates
        $("#gargul_min_date").change(refreshExport);
        $("#gargul_max_date").change(refreshExport);

        let refreshTimeout;
        function refreshExport() {
            refreshTimeout ? clearTimeout(refreshTimeout) : null;
            refreshTimeout = setTimeout(function () {
                let url = "{{ route('guild.export.gargul', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}?";

                let checked = $(checkboxSelector + ":checked");
                checked.each(function (i) {
                    url += "gargul_wishlist[]=" + checked[i].value + "&";
                });

                let minDate = $("#gargul_min_date").val();
                let maxDate = $("#gargul_max_date").val();
                // Convert from local to UTC for server, remove milliseconds for server
                minDate = minDate ? Math.floor(moment(minDate + ' 00:00:00').valueOf() / 1000) : '';
                // For max date, all times until end of day are valid
                maxDate = maxDate ? Math.floor(moment(maxDate + ' 23:59:59').valueOf() / 1000) : '';

                url += `gargul_min_date=${minDate}&gargul_max_date=${maxDate}&`;

                window.location.href = url;
            }, 1500);
        }
    });
</script>
<script src="{{ loadScript('clickToCopy.js') }}"></script>
@endsection
