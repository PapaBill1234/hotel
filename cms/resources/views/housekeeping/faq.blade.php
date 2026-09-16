@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">FAQ</span></div>
<div class="page_main"><div class="center">
@if(!empty($notice))<div class="clean-ok">{{ $notice }}</div>@endif
@if(!empty($error))<div class="clean-error">{{ $error }}</div>@endif
@if($action === 'create' || $action === 'edit')
<form method="post">@csrf
<input type="hidden" name="id" value="{{ (int) $entry->id }}">
<label>Category</label><br><input name="category" maxlength="100" value="{{ old('category', $entry->category) }}"><br>
<label>Question</label><br><input name="question" maxlength="255" value="{{ old('question', $entry->question) }}"><br>
<label>Answer</label><br><textarea name="answer">{{ old('answer', $entry->answer) }}</textarea><br>
<label>Display order</label><br><input name="sort_order" type="number" value="{{ old('sort_order', (int) $entry->sort_order) }}"><br>
<label><input name="active" type="checkbox" value="1"{{ (int) old('active', $entry->active) === 1 ? ' checked' : '' }}> Active</label><br>
<input type="submit" value="Save">
</form>
@else
<p><a href="/housekeeping/faq?do=create">New FAQ entry</a></p>
<table><tr><th>Category</th><th>Question</th><th>Order</th><th>Visible</th><th>Actions</th></tr>
@foreach($entries as $item)
<tr>
<td>{{ $item->category }}</td>
<td>{{ $item->question }}</td>
<td>{{ (int) $item->sort_order }}</td>
<td>{{ (int) $item->active === 1 ? 'Yes' : 'No' }}</td>
<td>
<a href="/housekeeping/faq?do=edit&id={{ (int) $item->id }}">Edit</a>
<form style="display:inline" method="post" action="/housekeeping/faq?do=delete">@csrf
<input type="hidden" name="id" value="{{ (int) $item->id }}"><button>Delete</button>
</form>
</td>
</tr>
@endforeach
</table>
@endif
</div></div>
@endsection
