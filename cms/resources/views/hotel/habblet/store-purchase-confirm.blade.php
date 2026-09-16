<div class="webstore-item-preview {{ $homes->itemCss($row->type, $row->data, true) }}">
	<div class="webstore-item-mask"></div>
</div>
<p>Are you sure you want to purchase this item?</p>
@if((int) $row->price > 0)
<p>PolarIS users.credits is not written from this website. Paid catalogue items stay unavailable here.</p>
@else
<p>You must be signed out of the hotel. Free items are added to your website inventory immediately.</p>
@endif
<p class="new-buttons">
<a href="#" class="new-button" id="webstore-confirm-cancel"><b>Cancel</b><i></i></a>
@if((int) $row->price === 0)
<a href="#" class="new-button" id="webstore-confirm-submit"><b>Continue</b><i></i></a>
@endif
</p>
<div class="clear"></div>
