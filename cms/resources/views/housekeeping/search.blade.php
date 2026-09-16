@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">Admin search</span></div>
<div class="page_main"><div class="center">
<form><input name="q" value="{{ $q }}" placeholder="User, email, IP, catalogue item, report"><button>Search</button></form>
@if($q !== '')
<h2>Users</h2>
<ul>
@forelse($users as $r)
<li>{{ (int) $r->id }} {{ $r->username }} {{ $r->mail }} {{ $r->ip_current }}</li>
@empty
@endforelse
</ul>
<h2>Catalogue</h2>
<ul>
@forelse($catalog as $r)
<li>{{ (int) $r->id }} {{ $r->catalog_name }}</li>
@empty
@endforelse
</ul>
<h2>Reports</h2>
<ul>
@forelse($reports as $r)
<li>#{{ (int) $r->id }} {{ $r->reason }} ({{ $r->status }})</li>
@empty
@endforelse
</ul>
@endif
</div></div>
@endsection
