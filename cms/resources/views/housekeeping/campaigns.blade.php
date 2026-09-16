@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">Campaigns</span></div>
<div class="page_main"><div class="center">
@if(!empty($notice))<div class="clean-ok">{{ $notice }}</div>@endif
@if(!empty($error))<div class="clean-error">{{ $error }}</div>@endif
@if($action === 'create' || $action === 'edit')
<form method="post">@csrf
<input type="hidden" name="id" value="{{ (int) $item->id }}">
<label>Name</label><br><input name="name" value="{{ old('name', $item->name) }}"><br>
<label>Description</label><br><input name="desc" value="{{ old('desc', $item->desc) }}"><br>
<label>Image URL</label><br><input name="image" value="{{ old('image', $item->image) }}"><br>
<label>Link URL</label><br><input name="url" value="{{ old('url', $item->url) }}"><br>
<label>Order</label><br><input type="number" name="sort_order" value="{{ old('sort_order', (int) $item->sort_order) }}"><br>
<label><input type="checkbox" name="visible" value="1"{{ old('visible', (string) $item->visible) === '1' ? ' checked' : '' }}> Visible</label><br>
<button>Save</button>
</form>
@else
<p><a href="/housekeeping/campaigns?do=create">New campaign</a></p>
<table><tr><th>Order</th><th>Name</th><th>Visible</th><th>Actions</th></tr>
@foreach($rows as $row)
<tr>
<td>{{ (int) $row->sort_order }}</td>
<td>{{ $row->name }}</td>
<td>{{ (string) $row->visible === '1' ? 'On' : 'Off' }}</td>
<td>
<a href="/housekeeping/campaigns?do=edit&id={{ (int) $row->id }}">Edit</a>
<form style="display:inline" method="post" action="/housekeeping/campaigns?do=delete">@csrf
<input type="hidden" name="id" value="{{ (int) $row->id }}"><button>Delete</button>
</form>
</td>
</tr>
@endforeach
</table>
@endif
</div></div>
@endsection
