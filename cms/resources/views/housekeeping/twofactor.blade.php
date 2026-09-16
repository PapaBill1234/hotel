@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">Staff 2FA</span></div>
<div class="page_main"><div class="center">
<p>Staff authenticator secrets would live in <code>phpretro_staff_totp</code>. This conversion will not fake authenticator success, and housekeeping sign-in still uses the PolarIS password only.</p>
@if($totp && (int) $totp->enabled === 1)
<p>A TOTP row is enabled for this account. The login challenge is not wired, so this page does not claim you are protected.</p>
@else
<p>No enabled authenticator for this staff account.</p>
@endif
</div></div>
@endsection
