@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">News</span></div>
<div class="page_main"><div class="center">
@if(!empty($notice))<div class="clean-ok">{{ $notice }}</div>@endif
@if(!empty($error))<div class="clean-error">{{ $error }}</div>@endif
@if($action === 'create' || $action === 'edit')
<form method="post">@csrf
<input type="hidden" name="id" value="{{ (int) $item->id }}">
<label>Title</label><br><input name="title" value="{{ old('title', $item->title) }}"><br>
<label>Summary</label><br><textarea name="summary">{{ old('summary', $item->summary) }}</textarea><br>
<label>Story</label><br><textarea name="story">{{ old('story', $item->story) }}</textarea><br>
<label>Author</label><br><input name="author" value="{{ old('author', $item->author) }}"><br>
<label>Categories</label><br><input name="categories" value="{{ old('categories', $item->categories) }}"><br>
<label>Image URLs</label><br><textarea name="images">{{ old('images', $item->images) }}</textarea><br>
<button>Save</button>
</form>
@else
<p><a href="/housekeeping/news?do=create">New article</a></p>
<table><tr><th>Title</th><th>Author</th><th>Categories</th><th>Date</th><th>Actions</th></tr>
@foreach($rows as $row)
<tr>
<td>{{ $row->title }}</td>
<td>{{ $row->author }}</td>
<td>{{ $row->categories }}</td>
<td>{{ date('Y-m-d H:i', (int) $row->time) }}</td>
<td>
<a href="/housekeeping/news?do=edit&id={{ (int) $row->id }}">Edit</a>
<form style="display:inline" method="post" action="/housekeeping/news?do=delete">@csrf
<input type="hidden" name="id" value="{{ (int) $row->id }}"><button>Delete</button>
</form>
</td>
</tr>
@endforeach
</table>
@endif
</div></div>
@endsection
