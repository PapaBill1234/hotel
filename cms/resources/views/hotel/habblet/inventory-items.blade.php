@if($type === 'widget')
<ul id="inventory-item-list">
@foreach($items as $row)
	@php $disabled = $homes->widgetPlaced($row->data); @endphp
	<li id="inventory-item-p-{{ (int) $row->id }}" title="{{ $row->name }}" class="webstore-widget-item{{ $disabled ? ' webstore-widget-disabled' : '' }}">
		<div class="webstore-item-preview {{ $homes->itemCss('widget', $row->data, true) }}">
			<div class="webstore-item-mask"></div>
		</div>
		<div class="webstore-widget-description">
			<h3>{{ $row->name }}</h3>
			<p>{{ $row->description }}</p>
		</div>
	</li>
@endforeach
</ul>
@else
@if(empty($items))
<div class="webstore-frank">
	<div class="blackbubble"><div class="blackbubble-body">
<p><b>Your inventory is empty.</b></p>
<p>Buy items from the Web Store.</p>
		<div class="clear"></div>
		</div></div>
	<div class="blackbubble-bottom"><div class="blackbubble-bottom-body">
			<img src="/web-gallery/images/box-scale/bubble_tail_small.gif" alt="" width="12" height="21" class="invitation-tail" />
		</div></div>
	<div class="webstore-frank-image"><img src="/web-gallery/images/frank/sorry.gif" alt="" width="57" height="88" /></div>
</div>
@endif
<ul id="inventory-item-list">
@php $i = 0; @endphp
@foreach($items as $row)
	@php $i++; @endphp
	<li id="inventory-item-{{ (int) $row->id }}" title="{{ $row->name }}">
		<div class="webstore-item-preview {{ $homes->itemCss($row->type, $row->data, true) }}">
			<div class="webstore-item-mask">
				@if((int) $row->quantity > 1)<div class="webstore-item-count"><div>x{{ (int) $row->quantity }}</div></div>@endif
			</div>
		</div>
	</li>
@endforeach
@for($n = 0; $n < $homes->padList($i); $n++)
	<li class="webstore-item-empty"></li>
@endfor
</ul>
@endif
