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
<style type="text/css">
#playground, #playground-outer { width: 752px; height: 1360px; }
</style>
@if(!empty($edit))
<script src="/web-gallery/static/js/homeedit.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
document.observe("dom:loaded", function() { initView({{ (int) $profile->id }}, {{ (int) $profile->id }}); });
function isElementLimitReached() {
	if (getElementCount() >= 200) {
		showHabboHomeMessageBox("Error", "You have reached the maximum number of items.", "Close");
		return true;
	}
	return false;
}
function cancelEditing(expired) {
	location.replace("/myhabbo/cancel/{{ (int) $profile->id }}" + (expired ? "?expired=true" : ""));
}
function getSaveEditingActionName(){
	return '/myhabbo/save';
}
function showEditErrorDialog() {
	var closeEditErrorDialog = function(e) { if (e) { Event.stop(e); } Element.remove($("myhabbo-error")); Overlay.hide(); }
	var dialog = Dialog.createDialog("myhabbo-error", "", false, false, false, closeEditErrorDialog);
	Dialog.setDialogBody(dialog, '<p>Error saving changes.</p><p><a href="#" class="new-button" id="myhabbo-error-close"><b>Close</b><i></i></a></p><div class="clear"></div>');
	Event.observe($("myhabbo-error-close"), "click", closeEditErrorDialog);
	Dialog.moveDialogToCenter(dialog);
	Dialog.makeDialogDraggable(dialog);
}
</script>
@endif
@endsection
@section('content')
<div id="mypage-wrapper" class="cbb blue">
<div class="box-tabs-container box-tabs-left clearfix">
@if(empty($edit) && !empty($isOwner))
	<a href="/myhabbo/startSession/{{ (int) $profile->id }}" id="edit-button" class="new-button dark-button edit-icon" style="float:left"><b><span></span>Edit</b><i></i></a>
@endif
	<h2 class="page-owner">{{ $profile->username }}</h2>
	<ul class="box-tabs"></ul>
</div>
<div id="mypage-content">
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
		'profile' => $profile,
		'edit' => !empty($edit),
		'hotelUser' => $hotelUser,
		'friends' => $friends,
		'friendCount' => $friendCount,
		'groups' => $groups,
		'rooms' => $rooms,
		'guestbook' => $guestbook,
	])
@endforeach
</div>
@if(!empty($edit))</div>@endif
				<div id="mypage-ad">
<div class="habblet "><div class="ad-container"></div></div>
				</div>
			</div>
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
	document.observe("dom:loaded", function() {
		initDraggableDialogs();
	});
</script>
@endif
@endsection
