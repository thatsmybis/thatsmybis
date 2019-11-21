<div class="form-group">
    <ul class="list-inline mb-2 text-muted">
        @if ($content->is_news)
            <li class="list-inline-item">
                <a href="{{ route('showUser', ['id' => $content->user->id, 'id' => $content->user->username]) }}">{{ $content->user->username }}</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
                <span class="js-watchable-timestamp" data-timestamp="{{ strtotime($content->created_at) }}"></span> ago
            </li>
        @endif
        <li class="list-inline-item">
            <a class="js-edit-content" data-id="{{ $content->id }}" href="">edit</a>
        </li>
        <li class="list-inline-item">
            <form id="removeContent{{ $content->id }}" class="form-horizontal" role="form" method="POST" action="{{ route('removeContent', $content->id) }}">
                {{ csrf_field() }}
                <button class="link" onClick="return confirm('Are you sure you want to remove this content?');">remove</button>
            </form>
        </li>
    </ul>
</div>

<div class="js-content" data-id="{{ $content->id }}" style="display:none;">
    <form class="form-horizontal" role="form" method="POST" action="{{ route('updateContent', $content->id) }}">
        {{ csrf_field() }}
        <div class="form-group mt-3">
            <label for="class">
                Title
            </label>
            <input name="title" maxlength="255" type="text" class="form-control" placeholder="Enter a title" value="{{ old('title') ? old('title') : $content->title }}" />
        </div>
        <div class="form-group">
            <label for="class">
                URL Title
            </label>
            <small class="text-muted">
                eg. "why_rag_is_boss"
            </small>
            <input name="slug" maxlength="255" type="text" class="form-control" placeholder="Type something_like_this" value="{{ old('slug') ? old('slug') : $content->slug }}" />
        </div>
        <input hidden name="is_news" value="{{ $content->is_news }}">
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
    <h1>{{ $content->title }}</h1>
</div>
<div class="js-markdown">
    {{ $content->content ? $content->content : '—' }}
</div>
