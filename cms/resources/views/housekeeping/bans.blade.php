@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">Bans</span></div>
<div class="page_main"><div class="center">
@if(!empty($notice))<div class="clean-ok">{{ $notice }}</div>@endif
@if(!empty($error))<div class="clean-error">{{ $error }}</div>@endif
<form method="post">@csrf
<input type="hidden" name="action" value="bulk_ban">
<input name="ban_reason" placeholder="Reason">
<input name="ban_expire" type="number" value="0" placeholder="Expiry timestamp">
<button>Ban selected</button>
<table><tr><th>Select</th><th>ID</th><th>User</th><th>IP</th><th>Issued</th><th>Expires</th><th>Reason</th><th>Type</th><th>Action</th></tr>
@foreach($rows as $row)
<tr>
<td><input type="checkbox" name="user_ids[]" value="{{ (int) $row->user_id }}"></td>
<td>{{ (int) $row->id }}</td>
<td>{{ (int) $row->user_id }}</td>
<td>{{ $row->ip }}</td>
<td>{{ date('Y-m-d H:i', (int) $row->timestamp) }}</td>
<td>{{ (int) $row->ban_expire > 0 ? date('Y-m-d H:i', (int) $row->ban_expire) : 'Permanent' }}</td>
<td>{{ $row->ban_reason }}</td>
<td>{{ $row->type }}</td>
<td>
<button type="submit" name="action" value="unban" formaction="/housekeeping/bans">Remove</button>
</td>
</tr>
@endforeach
</table>
</form>
</div></div>
@endsection
