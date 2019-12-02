@if (Auth::user()->hasRole('admin|guild_master|officer|raid_leader|class_leaders|raider'))
    <?php $fakeId = md5(strtotime('now')); ?>
    <div class="form-group">
        <ul class="list-inline mb-2">
            <li class="list-inline-item">
                <a class="js-edit-content" data-id="{{ $fakeId }}" href="">create new</a>
            </li>
        </ul>
    </div>

    <div class="js-content" data-id="{{ $fakeId }}" style="display:none;">
        <form class="form-horizontal" role="form" method="POST" action="{{ route('updateContent') }}">
            {{ csrf_field() }}
            <div class="form-group mt-3">
                <label for="class">
                    Title
                </label>
                <input name="title" maxlength="255" type="text" class="form-control" placeholder="Enter a title" value="{{ old('title') ? old('title') : '' }}" />
            </div>
            <div class="form-group">
                <label for="class">
                    URL Title
                </label>
                <small class="text-muted">
                    eg. "why_rag_is_boss"
                </small>
                <input name="slug" maxlength="255" type="text" class="form-control" placeholder="Type something_like_this" value="{{ old('slug') ? old('slug') : '' }}" />
            </div>
            <div class="form-group">
                <label for="class">
                    Post To
                </label>
                @if (Auth::user()->hasRole('admin|guild_master|officer'))
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="category" id="category1" value="news" checked>
                        <label class="form-check-label" for="category1">
                            News
                        </label>
                    </div>
                @endif
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category" id="category2" value="resource" checked>
                    <label class="form-check-label" for="category2">
                        Resources
                    </label>
                </div>
                @if (Auth::user()->hasRole('admin|guild_master|officer|raid_leader|raider'))
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="category" id="category3" value="myth_raid" checked>
                        <label class="form-check-label" for="category3">
                            Myth Raid
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="category" id="category4" value="night_raid" checked>
                        <label class="form-check-label" for="category4">
                            Night Raid
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="category" id="category5" value="weekend_raid" checked>
                        <label class="form-check-label" for="category5">
                            Weekend Raid
                        </label>
                    </div>
                @endif
            </div>
            <div class="">
                <small class="text-muted">format text with <a target="_blank" href="{{ env('LINK_MARKDOWN') }}">markdown</a></small>
            </div>
            <div class="form-group">
                <textarea name="content" rows="10" maxlength="64000" placeholder="" class="form-control">{{ old('content') ? old('content') : '' }}</textarea>
            </div>
            <div class="form-group">
                <button class="btn btn-success">Create</button>
            </div>
        </form>
    </div>
@endif
