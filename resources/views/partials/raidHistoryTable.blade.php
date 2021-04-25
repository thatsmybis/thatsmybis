<table id="raids" class="table table-border table-hover stripe">
    <thead>
        <tr>
            <th>
                <span class="fas fa-fw fa-helmet-battle text-muted"></span>
                Raid
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach($raids as $raid)
            @php
                $now = getDateTime();
                $isFuture = $raid->date > $now;
            @endphp
            <tr>
                <td>
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <ul class="list-inline mb-0">
                                @if ($raid->pivot->is_exempt)
                                    <li class="list-inline-item text-warning">
                                        Excused
                                    </li>
                                @elseif (!$isFuture)
                                    <li class="list-inline-item {{ getAttendanceColor($raid->pivot->credit) }}">
                                        {{ $raid->pivot->credit * 100 }}% credit
                                    </li>
                                @endif
                                <li class="list-inline-item">
                                    <span class="text-white font-weight-bold">
                                        {{ $raid->name }}
                                    </span>
                                    <span class="small">
                                        {{ $isFuture ? 'in' : '' }}
                                        <span class="js-watchable-timestamp js-timestamp-title" data-timestamp="{{ $raid->date }}"></span>
                                        {{ !$isFuture ? 'ago' : '' }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-6 col-12">
                            <ul class="list-inline mb-0">
                                <div>
                                    @if (isset($characters))
                                        @php
                                            $raidCharacter = $characters->where('id', $raid->pivot->character_id)->first();
                                        @endphp
                                        @if ($raidCharacter)
                                            <a href="{{ route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $character->id, 'nameSlug' => $character->name]) }}"
                                                class="font-weight-bold text-{{ strtolower($raidCharacter->class) }}">
                                                {{ $raidCharacter->name }}
                                            </a>
                                        @endif
                                    @endif
                                    @if (!$isFuture && $raid->pivot->remark_id)
                                        <span class="text-muted">
                                            {{ $remarks[$raid->pivot->remark_id] }}
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    @if ($raid->pivot->public_note)
                                        <span class="js-markdown-inline">{{ $raid->pivot->public_note }}</span>
                                    @endif
                                    @if (!$raid->pivot->public_note && ($isFuture || !$raid->pivot->remark_id) && (!isset($raidCharacter) || !$raidCharacter))
                                        —
                                    @endif
                                </div>
                                @if ($showOfficerNote && $raid->pivot->officer_note)
                                    <div>
                                        <span class="font-weight-bold small font-italic text-gold">Officer's Note</span>
                                        <span class="js-markdown-inline">{{ $raid->pivot->officer_note }}</span>
                                    </div>
                                @endif
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
