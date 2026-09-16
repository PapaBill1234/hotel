<div id="group-logo">
   <img src="/web-gallery/images/groups/group_icon.gif" alt="" width="46" height="46" />
</div>
<p>
Group name: <b>{{ $name }}</b>.<br>Price: <b>10 Coins</b>.<br> You have: <b>{{ $hotelUser->credits }} Coins</b>.
</p>
<p>This website cannot create PolarIS guilds. Club membership is granted in the hotel, not here.</p>
<div id="group-confirmation-button-area">
<div class="new-buttons clearfix">
	<a class="new-button" href="#" onclick="GroupPurchase.close(); return false;"><b>Cancel</b><i></i></a>
	<a class="new-button" href="#" onclick="GroupPurchase.purchase(); return false;"><b>Buy this</b><i></i></a>
</div>
</div>
