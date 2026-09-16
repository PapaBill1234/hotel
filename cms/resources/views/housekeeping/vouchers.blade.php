@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">Vouchers</span></div>
<div class="page_main"><div class="center">
@if(!empty($error))<div class="clean-error">{{ $error }}</div>@endif
@if(!empty($notice))<div class="clean-ok">{{ $notice }}</div>@endif
@if($action === 'create' || $action === 'edit')
<form method="post">
@csrf
<input type="hidden" name="id" value="{{ (int) $voucher->id }}">
<label>Code</label><br><input name="code" maxlength="10" value="{{ $voucher->code }}"><br>
<label>Credits</label><br><input type="number" name="credits" value="{{ (int) $voucher->credits }}"><br>
<label>Points</label><br><input type="number" name="points" value="{{ (int) $voucher->points }}"><br>
<label>Catalog item ID</label><br><input type="number" name="catalog_item_id" value="{{ (int) $voucher->catalog_item_id }}"><br>
<label>Amount</label><br><input type="number" name="amount" value="{{ (int) $voucher->amount }}"><br>
<button>Save</button>
</form>
<p>PolarIS vouchers are not written from this website.</p>
@else
<p><a href="/housekeeping/vouchers?do=create">New voucher</a></p>
<table>
<tr><th>Code</th><th>Credits</th><th>Points</th><th>Catalog item</th><th>Amount</th><th>Limit</th><th>Actions</th></tr>
@foreach($rows as $row)
<tr>
	<td>{{ $row->code }}</td>
	<td>{{ (int) $row->credits }}</td>
	<td>{{ (int) $row->points }}</td>
	<td>{{ (int) $row->catalog_item_id }}</td>
	<td>{{ (int) $row->amount }}</td>
	<td>{{ (int) ($row->limit ?? $row->redemption_limit ?? -1) }}</td>
	<td>
		<a href="/housekeeping/vouchers?do=edit&id={{ (int) $row->id }}">Edit</a>
		<form style="display:inline" method="post" action="/housekeeping/vouchers?do=delete">@csrf<input type="hidden" name="id" value="{{ (int) $row->id }}"><button>Delete</button></form>
	</td>
</tr>
@endforeach
</table>
@endif
</div></div>
@endsection
