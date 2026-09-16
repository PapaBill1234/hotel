@php
$canVote = !empty($hotelUser) && empty($summary['owner']) && empty($summary['mine']);
@endphp
<div id="rating-main">
@if($canVote)
<script type="text/javascript">
	var ratingWidget;
	document.observe("dom:loaded", function() {
		ratingWidget = new RatingWidget({{ (int) $ownerId }}, {{ (int) $widgetId }});
	});
</script>
<div class="rating-average">
	<b>Click on the stars to cast your vote!</b>
	<div id="rating-stars" class="rating-stars">
		<ul id="rating-unit_ul1" class="rating-unit-rating">
			<li class="rating-current-rating" style="width:0px;" />
			<li><a href="#" class="r1-unit rater">1</a></li>
			<li><a href="#" class="r2-unit rater">2</a></li>
			<li><a href="#" class="r3-unit rater">3</a></li>
			<li><a href="#" class="r4-unit rater">4</a></li>
			<li><a href="#" class="r5-unit rater">5</a></li>
		</ul>
	</div>
	{{ (int) $summary['total'] }} votes total
	<br/>
	({{ (int) $summary['high'] }} users voted 4 or better)
</div>
@else
<script type="text/javascript">
	var ratingWidget;
	ratingWidget = new RatingWidget({{ (int) $ownerId }}, {{ (int) $widgetId }});
</script>
<div class="rating-average">
	<b>Average rating: {{ $summary['average'] }}</b><br/>
	<div id="rating-stars" class="rating-stars">
		<ul id="rating-unit_ul1" class="rating-unit-rating">
			<li class="rating-current-rating" style="width:{{ (int) $summary['px'] }}px;" />
		</ul>
	</div>
	{{ (int) $summary['total'] }} votes total
	<br/>
	({{ (int) $summary['high'] }} users voted 4 or better)
</div>
@endif
</div>
