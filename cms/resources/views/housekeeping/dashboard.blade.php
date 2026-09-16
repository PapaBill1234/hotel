@extends('layouts.housekeeping')
@section('content')
<div class="page_title">
 <img src="/housekeeping/images/icons/dashboard.png" class="pticon">
 <span class="page_name_shadow">Dashboard</span>
 <span class="page_name">Dashboard</span>
</div>
<div class="page_main">
<table border="0" cellpadding="0" cellspacing="0" height="100%">
<tbody>
<tr height="100%">
<td class="page_main_left">
<div class="left_date">{{ $hkDate }}</div>
<div class="hr"></div>
<div class="loginuser"><strong>{{ $hotelUser->username }}</strong></div>
<div class="hr"></div>
<div class="text">
<p>Users: {{ $stats['users'] }}</p>
<p>Rooms: {{ $stats['rooms'] }}</p>
<p>Bans: {{ $stats['bans'] }}</p>
<p>Reports: {{ $stats['reports'] }}</p>
<p>Dau: {{ $stats['dau'] }}</p>
</div>
</td>
<td class="page_main_right">
<div class="center">
<h2>New registrations (14 days)</h2>
<table><tr><th>Date</th><th>Registrations</th></tr>
@forelse($days as $day)
<tr><td>{{ $day->day }}</td><td>{{ $day->total }}</td></tr>
@empty
<tr><td colspan="2">None</td></tr>
@endforelse
</table>
<p>Nothing to report</p>
<img src="/housekeeping/images/workman_habbo_down.gif" alt="" />
</div>
</td>
</tr>
</tbody>
</table>
</div>
@endsection
