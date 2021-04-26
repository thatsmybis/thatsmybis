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
                $limit = date('Y-m-d H:i:s', strtotime(getDateTime() . " - {$guild->attendance_decay_days} days"));
                $withinLimit = $raid->date > $limit;
             @endphp
            <tr>
                <td>
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <ul class="list-inline mb-0">
                                @if ($raid->pivot->is_exempt)
                                    <li class="list-inline-item {{ $withinLimit ? 'text-warning' : 'text-muted' }}">
                                        Excused
                                    </li>
                                @elseif (!$isFuture)
                                    <li class="list-inline-item {{ $withinLimit ? getAttendanceColor($raid->pivot->credit) : 'text-muted' }}"
                                        title="{{ !$withinLimit ? "outside of guild's counted attendance" : '' }}">
                                        {{ $raid->pivot->credit * 100 }}% credit
                                    </li>
                                @endif
                                <li class="list-inline-item">
                                    <a href="{{ route('guild.raids.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $raid->id, 'raidSlug' => $raid->name]) }}"
                                        class="text-white font-weight-bold">
                                        {{ $raid->name }}
                                    </a>
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
