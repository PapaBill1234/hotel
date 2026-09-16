@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">{{ $pageName }}</span></div>
<div class="page_main"><div class="center">
<p>{{ $stubMessage ?? ($pageName.' is not on this slice yet.') }}</p>
<p>This page will not fake vouchers, club, room transfer, or 2FA success.</p>
<p><a href="/housekeeping/dashboard">Back to dashboard</a></p>
</div></div>
@endsection
