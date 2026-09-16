@if(empty($items))
<div class="webstore-frank">
	<div class="blackbubble"><div class="blackbubble-body">
<p><b>No items in this category.</b></p>
<p>Watch this space.</p>
		<div class="clear"></div>
		</div></div>
	<div class="blackbubble-bottom"><div class="blackbubble-bottom-body">
			<img src="/web-gallery/images/box-scale/bubble_tail_small.gif" alt="" width="12" height="21" class="invitation-tail" />
		</div></div>
	<div class="webstore-frank-image"><img src="/web-gallery/images/frank/hello.gif" alt="" width="76" height="86" /></div>
</div>
@endif
<ul id="webstore-item-list">
@php $i = 0; @endphp
@foreach($items as $row)
	@php $i++; @endphp
	<li id="webstore-item-{{ (int) $row->id }}" title="{{ $row->name }}">
		<div class="webstore-item-preview {{ $homes->itemCss($row->type, $row->data, true) }}">
			<div class="webstore-item-mask">
				@if((int) $row->amount > 1)<div class="webstore-item-count"><div>x{{ (int) $row->amount }}</div></div>@endif
			</div>
		</div>
	</li>
@endforeach
@for($n = 0; $n < $homes->padList($i); $n++)
	<li class="webstore-item-empty"></li>
@endfor
</ul>
