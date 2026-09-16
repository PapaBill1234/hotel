@php
$key = $widget->widget_key;
$class = match ($key) {
    'guestbookwidget' => 'GuestbookWidget',
    'friendswidget' => 'FriendsWidget',
    'groupswidget' => 'GroupsWidget',
    'roomswidget' => 'RoomsWidget',
    'badgeswidget' => 'BadgesWidget',
    'ratingwidget' => 'RatingWidget',
    'highscoreswidget' => 'HighScoresWidget',
    'memberwidget' => 'MemberWidget',
    'groupinfowidget' => 'GroupInfoWidget',
    default => 'ProfileWidget',
};
$title = match ($key) {
    'guestbookwidget' => 'My Guestbook',
    'friendswidget' => 'My Friends ('.(int) ($friendCount ?? 0).')',
    'groupswidget' => 'My Groups',
    'roomswidget' => 'My Rooms',
    'badgeswidget' => 'Badges',
    'ratingwidget' => 'My Rating',
    'highscoreswidget' => 'High Scores',
    'memberwidget' => 'Members of this group',
    'groupinfowidget' => 'Group Info',
    default => 'My Profile',
};
$privacy = ($widget->privacy ?? 'public') === 'private' ? 'private' : 'public';
@endphp
<div class="movable widget {{ $class }}" id="widget-{{ $widget->id }}" style="{{ $homes->widgetStyle($widget) }}">
<div class="w_skin_defaultskin">
<div class="widget-corner" id="widget-{{ $widget->id }}-handle">
<div class="widget-headline"><h3>
@if(!empty($edit))
<img src="/web-gallery/images/myhabbo/icon_edit.gif" width="19" height="18" class="edit-button" id="widget-{{ $widget->id }}-edit" />
@endif
<span class="header-left">&nbsp;</span><span class="header-middle">{{ $title }}</span><span class="header-right">&nbsp;</span></h3>
</div>
</div>
<div class="widget-body"><div class="widget-content">
@if($key === 'profilewidget' && !empty($profile))
<div class="profile-info">
<div class="name" style="float: left"><span class="name-text">{{ $profile->username }}</span></div>
<br class="clear" />
<img alt="offline" src="/web-gallery/images/myhabbo/profile/habbo_{{ ((string) ($profile->online ?? '0') !== '0') ? 'online_anim' : 'offline' }}.gif" />
<div class="birthday text">Created on:</div>
<div class="birthday date">{{ !empty($profile->account_created) ? date('d-m-Y', (int) $profile->account_created) : '' }}</div>
<div class="profile-figure"><img alt="{{ $profile->username }}" src="{{ \App\Support\Hotel::avatarUrl($profile->look ?? '', 'b,4,4,,1,0') }}" /></div>
@if(!empty($profile->motto))<div class="profile-motto">{{ $profile->motto }}</div>@endif
<div id="profile-tags-container">No tags.</div>
</div>
@elseif($key === 'guestbookwidget')
<div id="guestbook-type" class="{{ $privacy }}">
<div id="guestbook-wrapper" class="gb-{{ $privacy === 'private' ? 'private' : 'public' }}">
<ul class="guestbook-entries" id="guestbook-entry-container">
@forelse(($guestbook ?? []) as $entry)
	@include('hotel.habblet.guestbook-entry', ['entry' => $entry, 'hotelUser' => $hotelUser ?? null])
@empty
<div id="guestbook-empty-notes">This guestbook has no entries.</div>
@endforelse
</ul>
</div>
</div>
@if(empty($edit) && !empty($hotelUser))
<div class="guestbook-toolbar clearfix">
<a href="#" class="new-button envelope-icon" id="guestbook-open-dialog"><b><span></span>New message</b><i></i></a>
</div>
@endif
@elseif($key === 'friendswidget')
<div id="avatar-list-search">
<input type="text" style="float:left;" id="avatarlist-search-string"/>
<a class="new-button" style="float:left;" id="avatarlist-search-button"><b>Search</b><i></i></a>
</div>
<br clear="all"/>
<div id="avatarlist-content">
@include('hotel.habblet.friend-list', ['friends' => $friends ?? []])
</div>
@elseif($key === 'groupswidget')
@if(empty($groups))
<p>You are not a member of any Groups</p>
@else
<ul class="groups-list">
@foreach($groups as $group)
	<li><a href="/groups/{{ (int) $group->id }}/id">{{ $group->name }}</a></li>
@endforeach
</ul>
@endif
@elseif($key === 'roomswidget')
@if(empty($rooms))
<p>No rooms.</p>
@else
<ul class="rooms-list">
@foreach($rooms as $room)
	<li>{{ $room->name }}</li>
@endforeach
</ul>
@endif
@elseif($key === 'highscoreswidget')
<table><tr><td>No high scores.</td></tr></table>
@elseif($key === 'badgeswidget')
<p>No badges.</p>
@elseif($key === 'ratingwidget')
@include('hotel.habblet.rating', [
	'ownerId' => (int) ($profile->id ?? $widget->user_id ?? 0),
	'widgetId' => (int) $widget->id,
	'summary' => $ratingSummary ?? ['total' => 0, 'high' => 0, 'average' => 0, 'px' => 0, 'mine' => false, 'owner' => true],
	'hotelUser' => $hotelUser ?? null,
])
@elseif($key === 'groupinfowidget' && !empty($guild))
<h4>{{ $guild->name }}</h4>
<p>Created on: <b>{{ !empty($guild->date_created) ? date('d-m-Y', (int) $guild->date_created) : '' }}</b></p>
<p><b>{{ (int) ($memberCount ?? 0) }}</b> users in group</p>
@if((int) ($guild->room_id ?? 0) > 0)
<p><a href="/client?forwardId=2&roomId={{ (int) $guild->room_id }}" class="group-info-room">Room</a></p>
@endif
<div class="group-info-description">{!! nl2br(e($guild->description)) !!}</div>
<p>Owner: {{ $ownerName ?? '' }}</p>
@elseif($key === 'memberwidget')
<ul class="habblet-list">
@forelse(($members ?? []) as $member)
	<li><a href="/home/{{ $member->username }}">{{ $member->username }}</a>@if((int) $member->level_id === 0) (owner)@elseif((int) $member->level_id === 1) (admin)@endif</li>
@empty
	<li>No members.</li>
@endforelse
</ul>
@endif
<div class="clear"></div>
</div></div>
</div>
</div>
