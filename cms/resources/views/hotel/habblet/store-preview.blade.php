@php $notEnough = ($credits - (int) $row->price) < 0; @endphp
<h4 title=""></h4>
<div id="webstore-preview-box"></div>
<div id="webstore-preview-price">
Price:<br /><b>
	{{ (int) $row->price }} Credits
</b>
</div>
<div id="webstore-preview-purse">
You have:<br /><b>{{ $credits }} Credits</b><br />
@if($notEnough)<span class="webstore-preview-error">Not enough credits</span><br />@endif
@if($inHotel)<span class="webstore-preview-error">Leave the hotel first. Credits are deducted from your hotel account and would be overwritten while you are online.</span><br />@endif
@if((int) $row->price > 0)<span class="webstore-preview-error">PolarIS users.credits is not written from this website. Only free items can be added here.</span><br />@endif
<a href="/credits" target=_blank>Get Credits</a>
</div>
<div id="webstore-preview-purchase" class="clearfix">
	<div class="clearfix">
		@if($notEnough || $inHotel || (int) $row->price > 0)<a href="#" class="new-button disabled-button" disabled="disabled" id="webstore-purchase-disabled">@else<a href="#" class="new-button" id="webstore-purchase">@endif<b>Purchase</b><i></i></a>
	</div>
</div>
<span id="webstore-preview-bg-text" style="display: none">Preview</span>
