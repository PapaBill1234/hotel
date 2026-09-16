@extends('layouts.hotel-community')
@section('head')
<link rel="stylesheet" href="/web-gallery/v2/styles/collectibles.css" type="text/css" />
@endsection
@section('content')
<div id="container">
	<div id="content" style="position: relative" class="clearfix">
	<div id="column1" class="column">
		<div class="habblet-container " id="collectible-current">
			<div class="cbb clearfix gray ">
				<h2 class="title">Current Collectable</h2>
				<div id="collectible-current-content" class="clearfix">
					<div id="collectibles-current-img" style="background-image: url({{ $currentCollectable->image ?? '' }})"></div>
					<h4>{{ $currentCollectable->name ?? 'No collectable' }}</h4>
					<p>{{ $monthLabel }}</p>
					<p class="last">{{ $currentCollectable->description ?? 'There is currently no collectable' }}</p>
				</div>
			</div>
		</div>
		<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>
		<div class="habblet-container ">
			<div class="cbb clearfix red ">
				<h2 class="title">Collectable Showroom</h2>
				<ul id="collectibles-list">
				@forelse($showroom as $index => $row)
					<li class="{{ $index % 2 ? 'even' : 'odd' }} clearfix">
						<div class="collectibles-prodimg" style="background-image: url({{ $row->image }})"></div>
						<h4>{{ date('F Y', (int) $row->time) }}: {{ $row->name }}</h4>
						<p class="collectibles-proddesc last">{{ $row->description }}</p>
					</li>
				@empty
				@endforelse
				</ul>
			</div>
		</div>
		<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>
	</div>
	<div id="column2" class="column">
		<div class="habblet-container ">
			<div class="cbb clearfix red ">
				<h2 class="title">What are Collectables?</h2>
				<div id="collectibles-instructions" class="box-content">Collectables are special furniture sold only for a limited and set period of time. Experienced {{ $shortname }}s would know them as rares. They always cost the same - 25 Credits.</div>
			</div>
		</div>
		<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>
		<div class="habblet-container ">
			<div class="cbb clearfix red ">
				<h2 class="title">Invest in Collectables</h2>
				<div class="box-content">
					<p class="collectibles-value-intro"><img src="/web-gallery/v2/images/collectibles/ukplane.png" alt="" width="79" height="47" />Collect your way to the riches!  Collectables not only make a great piece of Furni but also come with an amazing trade value.  As collectables will never be sold again (that's a promise), the value will keep increasing in time.</p>
					<p class="clear last"><img src="/web-gallery/v2/images/collectibles/chart.png" alt="" width="272" height="117" /></p>
				</div>
			</div>
		</div>
		<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>
	</div>
	</div>
</div>
@endsection
