<table id="raids" class="table table-border table-hover stripe">
    <thead>
        <tr>
            <th>
                <span class="fas fa-fw fa-helmet-battle text-muted"></span>
                {{ __("Raid") }}
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
                                    <li class="list-inline-item mr-0 {{ $withinLimit ? 'text-warning' : 'text-muted' }}">
                                        {{ __("Excused") }}
                                    </li>
                                @elseif (!$isFuture)
                                    <li class="list-inline-item mr-0 {{ $withinLimit ? getAttendanceColor($raid->pivot->credit) : 'text-muted' }}"
                                        title="{{ !$withinLimit ? "outside of guild's counted attendance" : '' }}">
                                        {{ $raid->pivot->credit * 100 }}% {{ __("credit") }}
                                    </li>
                                @endif
                                <li class="list-inline-item">
                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item mr-0">
                                            <a href="{{ route('guild.raids.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'raidId' => $raid->id, 'raidSlug' => $raid->slug]) }}"
                                                class="text-white font-weight-bold">
                                                {{ $raid->name }}
                                            </a>
                                        </li>
                                        <li class="list-inline-item mr-0">
                                            <span class="small">
                                                {{ $isFuture ? __('in') : '' }}
                                                <span class="js-watchable-timestamp js-timestamp-title" data-timestamp="{{ $raid->date }}"></span>
                                                {{ !$isFuture ? __('ago') : '' }}
                                            </span>
                                        </li>
                                        @if ($raid->cancelled_at)
                                            <li class="list-inline-item mr-0 small text-danger">
                                                {{ __('cancelled') }}
                                            </li>
                                        @endif
                                        @if ($raid->archived_at)
                                            <li class="list-inline-item mr-0 small text-warning">
                                                {{ __('archived') }}
                                            </li>
                                        @endif
                                        @if ($raid->ignore_attendance)
                                            <li class="list-inline-item mr-0 small text-warning">
                                                {{ __('attendance ignored') }}
                                            </li>
                                        @endif
                                    </ul>
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
                                            <a href="{{ route('character.show', ['guildId' => $guild->id, 'guildSlug' => $guild->slug, 'characterId' => $raidCharacter->id, 'nameSlug' => $raidCharacter->name]) }}"
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
                                        <span class="font-weight-bold small font-italic text-gold">{{ __("Officer's Note") }}</span>
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
