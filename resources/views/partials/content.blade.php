<div>
    <h4 class="text-druid font-weight-bold">
        {{ $content->title }} <small class="nowrap text-muted">#{{ $content->category }}</small>
    </h4>
</div>
<div class="js-content bg-dark mb-1 p-2 rounded" data-id="{{ $content->id }}" style="display:none;">
    <form class="form-horizontal" role="form" method="POST" action="{{ route('updateContent', $content->id) }}">
        {{ csrf_field() }}
        <div class="form-group mt-3">
            <label for="class">
                Title
                <span class="text-muted">#{{ $content->category }}</span>
            </label>
            <input name="title" maxlength="255" type="text" class="form-control" placeholder="Enter a title" value="{{ old('title') ? old('title') : $content->title }}" />
        </div>

        <input hidden name="category" value="{{ $content->category }}">

        <div class="">
            <small class="text-muted">format text with <a target="_blank" href="{{ env('LINK_MARKDOWN') }}">markdown</a></small>
        </div>
        <div class="form-group">
            <textarea name="content" rows="15" maxlength="64000" placeholder="" class="form-control">{{ old('content') ? old('content') : $content->content }}</textarea>
        </div>
        <div class="form-group">
            <button class="btn btn-success">Update</button>
        </div>
    </form>
</div>
<div>
    <ul class="list-inline mb-0 text-muted">
        @if ($content->category != 'resource')
            <li class="small list-inline-item">
                <a href="{{ route('showUser', ['id' => $content->user->id, 'id' => $content->user->username]) }}">{{ $content->user->username }}</a>
            </li>
            <li class="small list-inline-item">&sdot;</li>
            <li class="small list-inline-item">
                <span class="js-watchable-timestamp" data-timestamp="{{ strtotime($content->created_at) }}"></span> ago
            </li>
        @endif
        @if (Auth::user()->hasRole('admin|guild_master|officer|raid_leader|class_leader|raider'))
            <li class="small list-inline-item">
                <a class="js-edit-content" data-id="{{ $content->id }}" href="">edit</a>
            </li>
            <li class="small list-inline-item">
                <form id="removeContent{{ $content->id }}" class="form-horizontal" role="form" method="POST" action="{{ route('removeContent', $content->id) }}">
                    {{ csrf_field() }}
                    <button class="link" onClick="return confirm('Are you sure you want to remove this content?');">remove</button>
                </form>
            </li>
        @endif
    </ul>
</div>
<div class="js-markdown">
    {{ $content->content ? $content->content : '—' }}
</div>
