@extends('layouts.hotel-community')
@section('head')
<link rel="stylesheet" href="/web-gallery/styles/myhabbo/myhabbo.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/styles/myhabbo/skins.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/styles/myhabbo/dialogs.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/styles/myhabbo/buttons.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/styles/myhabbo/control.textarea.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/styles/myhabbo/boxes.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/v2/styles/myhabbo.css" type="text/css" />
<link href="/web-gallery/styles/myhabbo/assets.css" type="text/css" rel="stylesheet" />
<script src="/web-gallery/static/js/homeview.js" type="text/javascript"></script>
<link rel="stylesheet" href="/web-gallery/v2/styles/lightwindow.css" type="text/css" />
<script src="/web-gallery/static/js/homeauth.js" type="text/javascript"></script>
<link rel="stylesheet" href="/web-gallery/v2/styles/group.css" type="text/css" />
<style type="text/css">#playground, #playground-outer { width: 752px; height: 1360px; }</style>
@if(!empty($edit))
<script src="/web-gallery/static/js/homeedit.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
document.observe("dom:loaded", function() { initView({{ (int) $guild->id }}, {{ (int) $guild->id }}); });
function isElementLimitReached() {
	if (getElementCount() >= 200) {
		showHabboHomeMessageBox("Error", "You have reached the maximum number of items.", "Close");
		return true;
	}
	return false;
}
function cancelEditing(expired) {
	location.replace("/groups/actions/cancelEditingSession" + (expired ? "?expired=true" : ""));
}
function getSaveEditingActionName(){
	return '/groups/actions/saveEditingSession';
}
</script>
@endif
@endsection
@section('content')
<div id="mypage-wrapper" class="cbb blue">
<div class="box-tabs-container box-tabs-left clearfix">
@if(empty($edit) && !empty($isOwner))
	<a href="/groups/actions/startEditingSession/{{ (int) $guild->id }}" id="edit-button" class="new-button dark-button edit-icon" style="float:left"><b><span></span>Edit</b><i></i></a>
@endif
	<div class="myhabbo-view-tools">
		@if(!empty($hotelUser) && empty($isMember) && empty($edit))
			<a href="/groups/actions/join?groupId={{ (int) $guild->id }}" id="join-group-button">Join</a>
		@elseif(!empty($hotelUser) && !empty($isMember) && empty($isOwner) && empty($edit))
			<a href="/groups/actions/leave?groupId={{ (int) $guild->id }}" id="leave-group-button">Leave group</a>
		@endif
	</div>
	<h2 class="page-owner">{{ $guild->name }}</h2>
	<ul class="box-tabs">
		<li class="selected"><a href="/groups/{{ (int) $guild->id }}/id">Front Page</a><span class="tab-spacer"></span></li>
		<li><a href="/groups/{{ (int) $guild->id }}/id/discussions">Discussion Forum</a><span class="tab-spacer"></span></li>
	</ul>
</div>
<div id="mypage-content">
@if(!empty($joinNotice))<div class="box-content"><p>{{ $joinNotice }}</p></div>@endif
@if(!empty($edit))
<div id="top-toolbar" class="clearfix">
	<ul>
		<li><a href="#" id="inventory-button">Inventory</a></li>
		<li><a href="#" id="webstore-button">Web Store</a></li>
	</ul>
	<form action="#" method="get" style="width: 50%">
		<a id="cancel-button" class="new-button red-button cancel-icon" href="#"><b><span></span>Cancel</b><i></i></a>
		<a id="save-button" class="new-button green-button save-icon" href="#"><b><span></span>Save</b><i></i></a>
	</form>
</div>
@endif
<div id="mypage-bg" class="{{ $background }}">
@if(!empty($edit))<div id="playground-outer">@endif
<div id="playground">
@foreach($stickers as $item)
	@include('hotel.habblet.sticker', ['item' => $item, 'edit' => !empty($edit), 'homes' => $homes])
@endforeach
@foreach($stickies as $item)
	@include('hotel.habblet.stickie', ['item' => $item, 'edit' => !empty($edit), 'homes' => $homes])
@endforeach
@foreach($widgets as $widget)
	@include('hotel.partials.home-widget', [
		'homes' => $homes,
		'widget' => $widget,
		'edit' => !empty($edit),
		'hotelUser' => $hotelUser,
		'guild' => $guild,
		'members' => $members,
		'memberCount' => $memberCount,
		'ownerName' => $ownerName,
		'guestbook' => $guestbook,
	])
@endforeach
</div>
@if(!empty($edit))</div>@endif
</div>
</div>
</div>
@if(!empty($edit))
<script language="JavaScript" type="text/javascript">
initEditToolbar();
initMovableItems();
document.observe("dom:loaded", initDraggableDialogs);
Utils.setAllEmbededObjectsVisibility('hidden');
</script>
<div id="edit-save" style="display:none;"></div>
@else
<script type="text/javascript">
	Event.observe(window, "load", observeAnim);
	document.observe("dom:loaded", function() { initDraggableDialogs(); });
</script>
@endif
@endsection
