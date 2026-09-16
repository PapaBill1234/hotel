@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">Users</span></div>
<div class="page_main"><div class="center">
@if(!empty($error))<div class="clean-error">{{ $error }}</div>@endif
@if(!empty($notice))<div class="clean-ok">{{ $notice }}</div>@endif
@if($editUser)
<p>{{ $editUser->username }}</p>
<p>PolarIS users are shown read-only. Rank, credits, mail, pixels, and points are not written from this website.</p>
<form method="post" action="/housekeeping/users?do=savedetails">
@csrf
<input type="hidden" name="id" value="{{ (int) $editUser->id }}">
<label>Email</label><br><input name="mail" value="{{ $editUser->mail }}"><br>
<label>Rank</label><br><input name="rank" type="number" value="{{ (int) $editUser->rank }}"><br>
<label>Credits</label><br><input name="credits" type="number" value="{{ (int) $editUser->credits }}"><br>
<label>Pixels</label><br><input name="pixels" type="number" value="{{ (int) $editUser->pixels }}"><br>
<label>Points</label><br><input name="points" type="number" value="{{ (int) $editUser->points }}"><br>
<button>Save</button>
</form>
<p><a href="/housekeeping/users">Back to list</a></p>
@else
<form method="get"><input type="text" name="q" value="{{ $term }}"><button>Search</button></form>
<table>
<tr><th>ID</th><th>User</th><th>Email</th><th>Rank</th><th>Credits</th><th>Pixels</th><th>Points</th><th>Online</th></tr>
@foreach($users as $row)
<tr>
	<td>{{ (int) $row->id }}</td>
	<td><a href="/housekeeping/users?do=edit&id={{ (int) $row->id }}">{{ $row->username }}</a></td>
	<td>{{ $row->mail }}</td>
	<td>{{ (int) $row->rank }}</td>
	<td>{{ (int) $row->credits }}</td>
	<td>{{ (int) $row->pixels }}</td>
	<td>{{ (int) $row->points }}</td>
	<td>{{ $row->online }}</td>
</tr>
@endforeach
</table>
@endif
</div></div>
@endsection
