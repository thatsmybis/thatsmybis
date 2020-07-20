@if (Auth::user()->hasRole(env('PERMISSION_CLASS_LEADER')))
    <?php $fakeId = md5(strtotime('now')); ?>
    <div class="form-group">
        <ul class="list-inline mb-2">
            <li class="list-inline-item">
                <a class="js-edit-content btn btn-sm btn-light" data-id="{{ $fakeId }}" href="">New Post</a>
            </li>
            @include('partials/newsCategories')
        </ul>
    </div>

    <div class="js-content bg-light mb-1 p-2 rounded" data-id="{{ $fakeId }}" style="display:none;">
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
                    Post To
                </label>
                @if (Auth::user()->hasRole(env('PERMISSION_ADMIN')))
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="category" id="categoryA" value="news" {{ old('category') && old('category') == 'News' ? 'checked' : '' }}>
                        <label class="form-check-label" for="categoryA">
                            News
                        </label>
                    </div>
                @endif
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category" id="categoryB" value="resource" {{ old('category') && old('category') == 'resource' ? 'checked' : '' }}>
                    <label class="form-check-label" for="categoryB">
                        Resources
                    </label>
                </div>
                @if (Auth::user()->hasRole(env('PERMISSION_RAID_LEADER')))
                    @foreach ($raids as $raid)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="category" id="category{{ $raid->id }}" value="{{ $raid->id }}" {{ old('category') && old('category') == $raid->slug ? 'checked' : '' }}>
                            <label class="form-check-label" for="category{{ $raid->id }}">
                                {{ $raid->name }}
                            </label>
                        </div>
                    @endforeach
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
@else
    <div class="form-group">
        <ul class="list-inline mb-2">
            @include('partials/newsCategories')
        </ul>
    </div>
@endif
