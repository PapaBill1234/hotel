@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">Admin audit log</span></div>
<div class="page_main"><div class="center">
<form method="get">
<input name="admin_id" placeholder="Admin ID" value="{{ $filters['admin_id'] }}">
<input name="action" placeholder="Action type" value="{{ $filters['action'] }}">
<input name="target" placeholder="Target type" value="{{ $filters['target'] }}">
<button>Filter</button>
</form>
<table>
<tr><th>When</th><th>Admin</th><th>Action</th><th>Target</th><th>Details</th><th>IP</th></tr>
@forelse($rows as $r)
<tr>
	<td>{{ date('c', (int) $r->created_at) }}</td>
	<td>{{ $r->username }}</td>
	<td>{{ $r->action_type }}</td>
	<td>{{ $r->target_type }} #{{ (int) $r->target_id }}</td>
	<td>{{ $r->details }}</td>
	<td>{{ $r->ip }}</td>
</tr>
@empty
<tr><td colspan="6">No audit rows.</td></tr>
@endforelse
</table>
</div></div>
@endsection
