@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">Recommended</span></div>
<div class="page_main"><table border="0" cellpadding="0" cellspacing="0" height="100%"><tbody><tr height="100%"><td class="page_main_left">
<form method="post">@csrf<div class="text"><p>Search rooms by name or description.</p>
@if(count($searchResults))
<table>
@foreach($searchResults as $row)
<tr><td><a href="/housekeeping/recommended?do=create&recid={{ (int) $row->id }}">{{ $row->name }}</a></td><td>{{ (int) $row->id }}</td></tr>
@endforeach
</table>
@endif
<input type="text" name="query"><button type="submit" name="search" value="1">Search</button></div></form>
</td><td class="page_main_right"><div class="center">
@if(!empty($notice))<div class="clean-ok">{{ $notice }}</div>@endif
@if(!empty($error))<div class="clean-error">{{ $error }}</div>@endif
@if($action === 'create' || $action === 'edit')
<form method="post">@csrf
<input type="hidden" name="id" value="{{ (int) $item->id }}">
<label>Room / group id</label><br><input name="rec_id" value="{{ old('rec_id', $item->rec_id) }}"><br>
<label>Type</label><br>
<select name="type">
<option value="group"{{ $item->type === 'group' ? ' selected' : '' }}>Group</option>
<option value="room"{{ $item->type === 'room' ? ' selected' : '' }}>Room</option>
</select><br>
<label>Location</label><br>
<select name="sponsered">
<option value="0"{{ (string) $item->sponsered === '0' ? ' selected' : '' }}>Staff picks</option>
<option value="1"{{ (string) $item->sponsered === '1' ? ' selected' : '' }}>Recommended</option>
</select><br>
<button>Save</button>
</form>
@else
<p><a href="/housekeeping/recommended?do=create">New recommended</a></p>
<table><tr><th>ID</th><th>Type</th><th>Sponsored</th><th>Actions</th></tr>
@foreach($rows as $row)
<tr>
<td>{{ $row->rec_id }}</td>
<td>{{ $row->type }}</td>
<td>{{ $row->sponsered }}</td>
<td>
<a href="/housekeeping/recommended?do=edit&id={{ (int) $row->id }}">Edit</a>
<form style="display:inline" method="post" action="/housekeeping/recommended?do=delete">@csrf
<input type="hidden" name="id" value="{{ (int) $row->id }}"><button>Delete</button>
</form>
</td>
</tr>
@endforeach
</table>
@endif
</div></td></tr></tbody></table></div>
@endsection
