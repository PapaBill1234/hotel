@extends('layouts.hotel-community')
@section('content')
<div id="container"><div id="content" class="clearfix"><div id="column1" class="column">
<div class="habblet-container"><div class="cbb clearfix default">
<h2 class="title">Transaction history</h2>
<div class="box-content">
<table>
<tr><th>Date</th><th>Type</th><th>Amount</th><th>Balance</th><th>Description</th></tr>
@forelse($transactions as $transaction)
<tr>
	<td>{{ date('Y-m-d H:i', (int) $transaction->created_at) }}</td>
	<td>{{ $transaction->type }}</td>
	<td>{{ (int) $transaction->amount }}</td>
	<td>{{ (int) $transaction->balance_after }}</td>
	<td>{{ $transaction->description }}</td>
</tr>
@empty
@endforelse
</table>
@if($transactions === [] || count($transactions) === 0)
<p>No transactions have been recorded yet.</p>
@endif
</div>
</div></div>
</div></div></div>
@endsection
