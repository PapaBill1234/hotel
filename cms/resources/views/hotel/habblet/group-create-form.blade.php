<div id="group-purchase-header">
   <img src="/web-gallery/images/groups/group_icon.gif" alt="" width="46" height="46" />
</div>
<p>
Price: <b>10 Coins</b>.<br> You have: <b>{{ $hotelUser->credits }} Coins</b>.
</p>
<p>The group badge is a placeholder until you edit it in the hotel client. An owned room with no group is required.</p>
<form action="#" method="post" id="purchase-group-form-id">
<div id="group-name-area">
    <div id="group_name_message_error" class="error"></div>
    <label for="group_name" id="group_name_text">Group name:</label>
    <input type="text" name="group_name" id="group_name" maxlength="29" onKeyUp="GroupUtils.validateGroupElements('group_name', 29, 'Maximum group name length reached');" value=""/><br />
</div>
<div id="group-description-area">
    <div id="group_description_message_error" class="error"></div>
    <label for="group_description" id="description_text">Group description:</label>
    <span id="description_chars_left"><label for="characters_left">Characters left:</label>
    <input id="group_description-counter" type="text" value="250" size="3" readonly="readonly" class="amount" /></span><br/>
    <textarea name="group_description" id="group_description" onKeyUp="GroupUtils.validateGroupElements('group_description', 250, 'Maximum description length reached');"></textarea>
</div>
</form>
<div class="new-buttons clearfix">
	<a class="new-button" id="group-purchase-cancel-button" href="#" onclick="GroupPurchase.close(); return false;"><b>Cancel</b><i></i></a>
	<a class="new-button" href="#" onclick="GroupPurchase.confirm(); return false;"><b>Buy this</b><i></i></a>
</div>
