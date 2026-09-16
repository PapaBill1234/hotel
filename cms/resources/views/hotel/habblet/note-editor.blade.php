<form action="#" method="post" id="webstore-notes-form">
<input type="hidden" name="maxlength" id="webstore-notes-maxlength" value="{{ $rank > 5 ? '1.7976931348623157E+10308' : '500' }}" />
<div id="webstore-notes-counter">{{ $rank > 5 ? '∞' : 500 }}</div>
<p>
	<select id="webstore-notes-skin" name="skin">
			<option value="1" id="webstore-notes-skins-select-defaultskin"{{ $skin == 1 ? ' selected="selected"' : '' }}>Default</option>
			<option value="6" id="webstore-notes-skins-select-goldenskin"{{ $skin == 6 ? ' selected="selected"' : '' }}>Golden</option>
			@if($rank > 5)<option value="9" id="webstore-notes-skins-select-default"{{ $skin == 9 ? ' selected="selected"' : '' }}>Staff</option>@endif
			<option value="3" id="webstore-notes-skins-select-metalskin"{{ $skin == 3 ? ' selected="selected"' : '' }}>Metal</option>
			<option value="5" id="webstore-notes-skins-select-notepadskin"{{ $skin == 5 ? ' selected="selected"' : '' }}>Notepad</option>
			<option value="2" id="webstore-notes-skins-select-speechbubbleskin"{{ $skin == 2 ? ' selected="selected"' : '' }}>Speech bubble</option>
			<option value="4" id="webstore-notes-skins-select-noteitskin"{{ $skin == 4 ? ' selected="selected"' : '' }}>Note-it</option>
@if(!empty($hasClub))
			<option value="8" id="webstore-notes-skins-select-hc_pillowskin"{{ $skin == 8 ? ' selected="selected"' : '' }}>HC Pillow</option>
			<option value="7" id="webstore-notes-skins-select-hc_machineskin"{{ $skin == 7 ? ' selected="selected"' : '' }}>HC Machine</option>
@endif
	</select>
</p>
<p class="warning">Notes cannot be edited after they are placed.</p>
<div id="webstore-notes-edit-container">
<textarea id="webstore-notes-text" rows="7" cols="42" name="noteText">{{ $note }}</textarea>
</div>
<p>
<a href="#" class="new-button" id="n-cancel"><b>Cancel</b><i></i></a>
<a href="#" class="new-button" id="n-place"><b>Place</b><i></i></a>
</p>
</form>
