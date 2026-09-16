@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">Banners</span></div>
<div class="page_main"><div class="center">
@if(!empty($notice))<div class="clean-ok">{{ $notice }}</div>@endif
@if(!empty($error))<div class="clean-error">{{ $error }}</div>@endif
@if($action === 'create' || $action === 'edit')
<form method="post">@csrf
<input type="hidden" name="id" value="{{ (int) $item->id }}">
<label>Text</label><br><input name="text" value="{{ old('text', $item->text) }}"><br>
<label>Image URL</label><br><input name="banner" value="{{ old('banner', $item->banner) }}"><br>
<label>Link URL</label><br><input name="url" value="{{ old('url', $item->url) }}"><br>
<label>HTML (advanced)</label><br><textarea name="html">{{ old('html', $item->html) }}</textarea><br>
<label>Order</label><br><input type="number" name="sort_order" value="{{ old('sort_order', (int) $item->sort_order) }}"><br>
<label><input type="checkbox" name="status" value="1"{{ old('status', (string) $item->status) === '1' ? ' checked' : '' }}> Visible</label><br>
<button>Save</button>
</form>
@else
<p><a href="/housekeeping/banners?do=create">New banner</a></p>
<table><tr><th>Order</th><th>Data</th><th>Visible</th><th>Actions</th></tr>
@foreach($rows as $row)
<tr>
<td>{{ (int) $row->sort_order }}</td>
<td>{{ (string) $row->advanced === '1' ? 'HTML' : ($row->banner !== '' ? $row->banner : $row->text) }}</td>
<td>{{ (string) $row->status === '1' ? 'On' : 'Off' }}</td>
<td>
<a href="/housekeeping/banners?do=edit&id={{ (int) $row->id }}">Edit</a>
<form style="display:inline" method="post" action="/housekeeping/banners?do=delete">@csrf
<input type="hidden" name="id" value="{{ (int) $row->id }}"><button>Delete</button>
</form>
</td>
</tr>
@endforeach
</table>
@endif
</div></div>
@endsection
