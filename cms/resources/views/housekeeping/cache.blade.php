@extends('layouts.housekeeping')
@section('content')
<div class="page_title">
 <img src="/housekeeping/images/icons/cache.png" class="pticon">
 <span class="page_name_shadow">Cache</span>
 <span class="page_name">Cache</span>
</div>
<div class="page_main">
<table border="0" cellpadding="0" cellspacing="0" height="100%">
<tbody>
<tr height="100%">
<td class="page_main_left">
<div class="left_date">{{ $hkDate }}</div>
<div class="hr"></div>
<div class="text">
<p>If you turned caching on, and you modify some settings outside of housekeeping, there's a chance that your cache is outdated.</p>
</div>
<div class="hr"></div>
<div class="text">
<p>Visit the <a href="/housekeeping/settings">settings page</a> to change the cache settings.</p>
</div>
</td>
<td class="page_main_right">
<div class="center">
<div class="clean-{{ $cacheCode }}">{{ $cacheMessage }}</div>
</div>
</td>
</tr>
</tbody>
</table>
</div>
@endsection
