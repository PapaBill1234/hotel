@extends('layouts.housekeeping')
@section('content')
<div class="page_title">
 <img src="/housekeeping/images/icons/alerts.png" class="pticon">
 <span class="page_name_shadow">Alerts</span>
 <span class="page_name">Alerts</span>
</div>
<div class="page_main">
<table border="0" cellpadding="0" cellspacing="0" height="100%">
<tbody>
<tr height="100%">
<td class="page_main_left">
<div class="left_date">{{ $hkDate }}</div>
<div class="hr"></div>
<div class="text">
 <p>@if($do === 'create')
  @if($type === 'single')
   Send a PolarIS alertuser command. The target must be online in the hotel.
  @else
   Send a PolarIS hotelalert command. Only users currently online in the hotel will see it.
  @endif
 @else
  PolarIS has no alerts table. Hotel alerts reach online users only and are not stored on the website.
 @endif</p>
 </div>
<form name="users_search" action="/housekeeping/alerts" method="POST">@csrf
 <div class="text">
   <div class="user_listview_bg">
    <div id="listview">
     <div class="Scroller-Container">
<table border="0" cellspacing="0" cellpadding="0">
@if(count($searchResults))
@foreach($searchResults as $i => $row)
<tr id="{{ $i % 2 === 0 ? 'even' : 'odd' }}"><td><a href="/housekeeping/alerts?type=single&do=create&userid={{ (int) $row->id }}">{{ $row->username }}</a></td><td class="selectid">{{ (int) $row->id }}</td></tr>
@endforeach
@else
<tr><td><b>To find a user, search their name and click on it to send him/her an alert.</b></td></tr>
@endif
</table>
     </div>
    </div>
   </div>
<div class="searcuser">
<input type="text" name="query" id="searchname" value="">
<button type="submit" name="search" id="button_search">Search</button>
</div>
</div>
</form>
</td>
 <td class="page_main_right">
<div class="center">
@if(!empty($formError))<div class="clean-error">{{ $formError }}</div>@endif
@if(!empty($message))<div class="clean-ok">{{ $message }}</div>@endif
@if($do === 'create')
<div class="settings">
<form name="settings" action="/housekeeping/alerts?type={{ $type }}&do=create" method="POST">@csrf
@if($type === 'single')
<label for="userid">User ID:</label><br /><input type="text" name="userid" value="{{ $userid ?: '' }}" title="The ID of the user you want to alert. Use the search box on the left if you don't know." /><br />
@endif
<label for="alert">Alert:</label><br /><textarea name="alert" title="The hotel alert text. PolarIS does not persist this.">{{ $alert }}</textarea><br />
<p>This is sent through PolarIS RCON/CMS (alertuser or hotelalert). Offline users are not queued.</p>
<div class="button"><input type="submit" name="save" value="Send" /></div>
</form>
</div>
@else
<div class="contentdisplay">
<p>There is no unread-alert queue. Send a hotel alert to an online user or broadcast to the hotel.</p>
<div class="button"><input type="button" value="Mass Alert" onclick="window.location.href='/housekeeping/alerts?type=mass&do=create'"></input></div>
<div class="button"><input type="button" value="User Alert" onclick="window.location.href='/housekeeping/alerts?type=single&do=create'"></input></div>
</div>
@endif
</div>
 </td>
</tr>
</tbody>
</table>
</div>
@endsection
