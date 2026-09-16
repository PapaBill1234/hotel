@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">Logs</span></div>
<div class="page_main"><table border="0" cellpadding="0" cellspacing="0" height="100%"><tbody><tr height="100%"><td class="page_main_left">
<form method="post">@csrf
<div class="text"><p>Search rooms, then open chat logs for that room. Source is PolarIS <code>chatlogs_room</code>.</p>
@if(count($searchResults))
<table>
@foreach($searchResults as $row)
<tr><td><a href="/housekeeping/logs?roomid={{ (int) $row->id }}">{{ $row->name }}</a></td><td>{{ (int) $row->id }}</td></tr>
@endforeach
</table>
@endif
<input type="text" name="query"><button type="submit" name="search" value="1">Search</button></div></form>
</td><td class="page_main_right"><div class="center">
<table><tr><th>User</th><th>Message</th><th>Room</th><th>Time</th></tr>
@forelse($rows as $row)
<tr><td>{{ $row->username }}</td><td>{{ $row->message }}</td><td>{{ (int) $row->room_id }}</td><td>{{ date('Y-m-d H:i:s', (int) $row->timestamp) }}</td></tr>
@empty
@endforelse
</table>
</div></td></tr></tbody></table></div>
@endsection
