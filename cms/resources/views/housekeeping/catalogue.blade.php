@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">Catalogue</span></div>
<div class="page_main"><div class="center">
@if(!empty($notice))<div class="clean-ok">{{ $notice }}</div>@endif
@if(!empty($error))<div class="clean-error">{{ $error }}</div>@endif
@if($action === 'create' || $action === 'edit')
<form method="post">@csrf
<input type="hidden" name="id" value="{{ (int) $item->id }}">
<label>Name</label><br><input name="name" value="{{ old('name', $item->name) }}"><br>
<label>Description</label><br><input name="desc" value="{{ old('desc', $item->desc) }}"><br>
<label>Type</label><br>
<select name="type">
@foreach($types as $value => $label)
<option value="{{ $value }}"{{ (string) old('type', $item->type) === (string) $value ? ' selected' : '' }}>{{ $label }}</option>
@endforeach
</select><br>
<label>Data</label><br><input name="data" value="{{ old('data', $item->data) }}"><br>
<label>Price</label><br><input type="number" name="price" value="{{ old('price', (int) $item->price) }}"><br>
<label>Amount</label><br><input type="number" name="amount" value="{{ old('amount', (int) $item->amount) }}"><br>
<label>Min rank</label><br><input type="number" name="minrank" value="{{ old('minrank', (int) $item->minrank) }}"><br>
<label>Where</label><br>
<select name="where">
@foreach($placements as $value => $label)
<option value="{{ $value }}"{{ (string) old('where', $item->where) === (string) $value ? ' selected' : '' }}>{{ $label }}</option>
@endforeach
</select><br>
<label>Category</label><br><input name="category" value="{{ old('category', $item->category) }}"><br>
<button>Save</button>
</form>
@else
<p><a href="/housekeeping/catalogue?do=create">New item</a></p>
<table><tr><th>Type</th><th>Name</th><th>Data</th><th>Category</th><th>Actions</th></tr>
@foreach($rows as $row)
<tr>
<td>{{ $types[(string) $row->type] ?? $row->type }}</td>
<td>{{ $row->name }}</td>
<td>{{ $row->data }}</td>
<td>{{ $row->category }}</td>
<td>
<a href="/housekeeping/catalogue?do=edit&id={{ (int) $row->id }}">Edit</a>
<form style="display:inline" method="post" action="/housekeeping/catalogue?do=delete">@csrf
<input type="hidden" name="id" value="{{ (int) $row->id }}"><button>Delete</button>
</form>
</td>
</tr>
@endforeach
</table>
@endif
</div></div>
@endsection
