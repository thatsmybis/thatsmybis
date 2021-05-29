@extends('layouts.app')
@section('title', $raidGroup->name . ' Mains - ' . config('app.name'))

@section('content')
<div class="container-fluid container-width-capped">
    <div class="row">
        <div class="col-xl-8 offset-xl-2 col-md-10 offset-md-1 col-12">
            <div class="row">
                <div class="col-12 mt-3 mb-3">
                    <a href="{{ route('guild.raidGroups', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}" class="btn btn-primary">
                        <span class="fas fa-fw fa-arrow-left"></span> Raid Groups
                    </a>
                </div>
                <div class="col-12 pt-2 mb-2">
                    <h1 class="font-weight-medium">
                        <span class="fas fa-fw fa-helmet-battle text-dk"></span>
                        <span style="{{ $raidGroup->role ? 'color:' . $raidGroup->getColor() : '' }}">{{ $raidGroup->name }}</span> Main Raiders
                    </h1>
                    {{ $raidGroup->secondary_characters_count }} other raider{{ $raidGroup->secondary_characters_count != 1 ? 's' : '' }}
                </div>
                <!-- Doing some funky flex and 100% width magic to get the lists to take up the whole height -->
                <div class="d-flex col-6 pr-2 pt-3 pb-1 mb-2 bg-light rounded">
                    <form id="characterForm" class="d-flex flex-column w-100 form-horizontal" role="form" method="POST" action="{{ route('guild.raidGroup.updateCharacters', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) }}">
                        {{ csrf_field() }}
                        <input hidden name="raid_group_id" value="{{ $raidGroup->id }}" />

                        <div>
                            <h4>
                                Selected &nbsp; <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> Save</button>
                            </h4>
                        </div>
                        <div class="d-flex" style="flex:1"> <!-- flex:1 will make it take up the full vertical space -->
                            <ul id="selectedCharacters" class="w-100 sortable-no-empty no-bullet no-indent pt-3 pl-3 pr-3 pb-2 bg-dark rounded">
                                @foreach ($raidGroup->characters as $character)
                                    @include('guild/raidGroups/partials/movableCharacter')
                                @endforeach
                            </ul>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-success"><span class="fas fa-fw fa-save"></span> Save</button>
                        </div>
                    </form>
                </div>
                <div class="d-flex w-100 flex-column col-6 pl-2 pt-3 pb-1 mb-2 bg-light rounded">
                    <div>
                        <h4>Available</h4>
                    </div>
                    <div class="d-flex" style="flex:1">
                        <ul id="availableCharacters" class="w-100 sortable-no-empty no-bullet no-indent pt-3 pl-3 pr-3 pb-2 bg-dark rounded">
                            @foreach ($guild->characters as $character)
                                @include('guild/raidGroups/partials/movableCharacter')
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('scripts')
<script>
    $(document).ready(function () {
        warnBeforeLeaving("#characterForm");
        $("#selectedCharacters" ).sortable({
          connectWith: "#availableCharacters",
          handle: ".js-sort-handle",
          // Sort both lists once something has been dragged
          stop: function(event, ui) {
            sort("#availableCharacters");
            sort("#selectedCharacters");
          },
        });
        $("#availableCharacters" ).sortable({
          connectWith: "#selectedCharacters",
          handle: ".js-sort-handle",
          // Sort both lists once something has been dragged
          stop: function(event, ui) {
            sort("#availableCharacters");
            sort("#selectedCharacters");
          },
        });
    });



    // Sort a list by its items' data-val attribute
    function sort(id) {
        var sortableList = $(id);
        var listItems = $("li", sortableList);
        listItems.sort(function (a, b) {
            return ($(a).data("val") > $(b).data("val"))  ? 1 : -1;
        });
        sortableList.append(listItems);
        // Trigger a change event
        $(id).change();
    }
</script>
@endsection
