<div style="position: relative;">
<div id="webstore-categories-container">
	<h4>Categories:</h4>
	<div id="webstore-categories">
<ul class="purchase-main-category">
		<li id="maincategory-1-stickers" class="selected-main-category webstore-selected-main">
			<div>Stickers</div>
			<ul class="purchase-subcategory-list" id="main-category-items-1">
@foreach($stickers as $row)
				<li id="subcategory-1-{{ (int) $row->category_id }}-stickers" class="subcategory">
					<div>{{ $row->category }}</div>
				</li>
@endforeach
			</ul>
		</li>
		<li id="maincategory-4-backgrounds" class="main-category">
			<div>Backgrounds</div>
			<ul class="purchase-subcategory-list" id="main-category-items-4">
@foreach($backgrounds as $row)
				<li id="subcategory-4-{{ (int) $row->category_id }}-backgrounds" class="subcategory">
					<div>{{ $row->category }}</div>
				</li>
@endforeach
			</ul>
		</li>
		<li id="maincategory-3-stickie_notes" class="main-category-no-subcategories">
			<div>Notes</div>
			<ul class="purchase-subcategory-list" id="main-category-items-3">
@foreach($notes as $row)
				<li id="subcategory-3-{{ (int) $row->category_id }}-stickie_notes" class="subcategory">
					<div>{{ $row->category }}</div>
				</li>
@endforeach
			</ul>
		</li>
</ul>
	</div>
</div>
<div id="webstore-content-container">
	<div id="webstore-items-container">
		<h4>Select an item by clicking it</h4>
		<div id="webstore-items"><ul id="webstore-item-list">
@for($n = 0; $n < 20; $n++)<li class="webstore-item-empty"></li>@endfor
</ul></div>
	</div>
	<div id="webstore-preview-container">
		<div id="webstore-preview-default"></div>
		<div id="webstore-preview"></div>
	</div>
</div>
<div id="inventory-categories-container">
	<h4>Categories:</h4>
	<div id="inventory-categories">
<ul class="purchase-main-category">
	<li id="inv-cat-stickers" class="selected-main-category-no-subcategories"><div>Stickers</div></li>
	<li id="inv-cat-backgrounds" class="main-category-no-subcategories"><div>Backgrounds</div></li>
	<li id="inv-cat-widgets" class="main-category-no-subcategories"><div>Widgets</div></li>
	<li id="inv-cat-notes" class="main-category-no-subcategories"><div>Notes</div></li>
</ul>
	</div>
</div>
<div id="inventory-content-container">
	<div id="inventory-items-container">
		<h4>Select an item by clicking it</h4>
		<div id="inventory-items"><ul id="inventory-item-list">
@for($n = 0; $n < 20; $n++)<li class="webstore-item-empty"></li>@endfor
</ul></div>
	</div>
	<div id="inventory-preview-container">
		<div id="inventory-preview-default"></div>
		<div id="inventory-preview"></div>
	</div>
</div>
<div id="webstore-close-container">
	<div class="clearfix"><a href="#" id="webstore-close" class="new-button"><b>Close</b><i></i></a></div>
</div>
</div>
