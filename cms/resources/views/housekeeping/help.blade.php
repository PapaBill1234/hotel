@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">Help</span></div>
<div class="page_main"><div class="center">
@if(!empty($notice))<div class="clean-ok">{{ $notice }}</div>@endif
@if($action === 'remove' && $removeId > 0)
<form method="post" action="/housekeeping/help?do=remove">@csrf
<input type="hidden" name="id" value="{{ $removeId }}">
<p>Remove this ticket?</p>
<button type="submit" name="remove" value="1">Remove</button>
</form>
@else
<table>
<tr><th>Subject</th><th>Message</th><th>Username</th><th>Email</th><th>Date</th><th>Status</th><th>Actions</th></tr>
@foreach($rows as $row)
<tr>
<td>{{ $row->subject }}</td>
<td>{{ \Illuminate\Support\Str::limit((string) $row->message, 120) }}</td>
<td>{{ $row->username }}</td>
<td>{{ $row->email }}</td>
<td>{{ date('n/j/Y g:i A', (int) $row->created_at) }}</td>
<td>{{ $row->status }}</td>
<td>
<a href="/housekeeping/help?do=pickup&id={{ (int) $row->id }}">Pick up</a>
<a href="/housekeeping/help?do=remove&id={{ (int) $row->id }}">Remove</a>
</td>
</tr>
@endforeach
</table>
@endif
</div></div>
@endsection
