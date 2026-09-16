@extends('layouts.housekeeping')
@section('content')
<div class="page_title">
 <img src="/housekeeping/images/icons/about.png" class="pticon">
 <span class="page_name_shadow">About</span>
 <span class="page_name">About</span>
</div>
<div class="page_main">
<table border="0" cellpadding="0" cellspacing="0" height="100%">
<tbody>
<tr height="100%">
<td class="page_main_left">
<div class="left_date">{{ $hkDate }}</div>
<div class="hr"></div>
<div class="loginuser"><strong>PHPRetro Version {{ $version }} {{ $status }}</strong></div>
<div class="hr"></div>
<div class="text">
Revision: {{ $revision }}<br />
Build: {{ $build }}<br /><br />
PHPRetro is, always has been, and always will be, free software. <strong>If you paid for this software, please report the seller and demand your money back.</strong>
This Laravel conversion keeps PolarIS tables read-only except the signed write allowlist.
</div>
</td>
<td class="page_main_right">
<div class="text">
<h1>Coders</h1>
<div class="credits">
<table border="0" cellpadding="0" cellspacing="0">
<tr><td width="25%"><strong>Yifan Lu</strong></td><td width="75%">Main coder</td></tr>
<tr><td width="25%"><strong>Meth0d</strong></td><td width="75%">Original HoloCMS coder, PHPRetro uses parts from HoloCMS</td></tr>
</table>
</div>
</div>
</td>
</tr>
</tbody>
</table>
</div>
@endsection
