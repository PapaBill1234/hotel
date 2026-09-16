@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">User reports</span></div>
<div class="page_main"><div class="center">
@if(!empty($notice))<div class="clean-ok">{{ $notice }}</div>@endif
<table><tr><th>ID</th><th>Reporter</th><th>Reported</th><th>Reason</th><th>Evidence</th><th>Status</th><th>Action</th></tr>
@foreach($rows as $r)
<tr>
<td>{{ (int) $r->id }}</td>
<td>{{ $r->reporter }}</td>
<td>{{ $r->reported }}</td>
<td>{{ $r->reason }}</td>
<td>{{ $r->evidence }}</td>
<td>{{ $r->status }}</td>
<td>
<form method="post">@csrf
<input name="id" type="hidden" value="{{ (int) $r->id }}">
<select name="status">
<option{{ $r->status === 'open' ? ' selected' : '' }}>open</option>
<option{{ $r->status === 'assigned' ? ' selected' : '' }}>assigned</option>
<option{{ $r->status === 'resolved' ? ' selected' : '' }}>resolved</option>
<option{{ $r->status === 'dismissed' ? ' selected' : '' }}>dismissed</option>
</select>
<input name="assigned_to" type="number" placeholder="staff ID">
<input name="action_notes" placeholder="notes">
<button>Save</button>
</form>
</td>
</tr>
@endforeach
</table>
</div></div>
@endsection
