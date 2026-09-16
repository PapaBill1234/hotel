@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">Staff sessions</span></div>
<div class="page_main"><div class="center">
@if(!empty($notice))<div class="clean-ok">{{ $notice }}</div>@endif
<table>
<tr><th>Staff</th><th>IP</th><th>Created</th><th>Last activity</th><th>Action</th></tr>
@forelse($rows as $r)
<tr>
	<td>{{ $r->username }}</td>
	<td>{{ $r->ip }}</td>
	<td>{{ date('c', (int) $r->created_at) }}</td>
	<td>{{ date('c', (int) $r->last_activity) }}</td>
	<td><form method="post">@csrf<input type="hidden" name="id" value="{{ (int) $r->id }}"><button>Force logout</button></form></td>
</tr>
@empty
<tr><td colspan="5">No active website staff sessions.</td></tr>
@endforelse
</table>
</div></div>
@endsection
