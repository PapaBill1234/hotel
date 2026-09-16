@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">Collectibles</span></div>
<div class="page_main"><div class="center">
@if(!empty($notice))<div class="clean-ok">{{ $notice }}</div>@endif
@if(!empty($error))<div class="clean-error">{{ $error }}</div>@endif
@if($action === 'create' || $action === 'edit')
<form method="post">@csrf
<input type="hidden" name="id" value="{{ (int) $item->id }}">
<label>Name</label><br><input name="name" value="{{ old('name', $item->name) }}"><br>
<label>Description</label><br><textarea name="description">{{ old('description', $item->description) }}</textarea><br>
<label>Image URL</label><br><input name="image" value="{{ old('image', $item->image) }}"><br>
<label>Month timestamp</label><br><input type="number" name="time" value="{{ old('time', (int) $item->time) }}"><br>
<button>Save</button>
</form>
@else
<p><a href="/housekeeping/collectables?do=create">New collectible</a></p>
<table><tr><th>Name</th><th>Month</th><th>Image</th><th>Actions</th></tr>
@foreach($rows as $row)
<tr>
<td>{{ $row->name }}</td>
<td>{{ date('F Y', (int) $row->time) }}</td>
<td>{{ $row->image }}</td>
<td>
<a href="/housekeeping/collectables?do=edit&id={{ (int) $row->id }}">Edit</a>
<form style="display:inline" method="post" action="/housekeeping/collectables?do=delete">@csrf
<input type="hidden" name="id" value="{{ (int) $row->id }}"><button>Delete</button>
</form>
</td>
</tr>
@endforeach
</table>
@endif
</div></div>
@endsection
