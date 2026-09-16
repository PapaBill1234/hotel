@extends('layouts.hotel-community')
@section('content')
<div id="container">
	<div id="content" style="position: relative" class="clearfix">
    <div id="column1" class="column">
		<div class="habblet-container ">
			<div class="cbb clearfix red ">
				<h2 class="title">Page not found!</h2>
				<div id="notfound-content" class="box-content">
					<p class="error-text">Sorry, but the page you were looking for was not found.</p>
					<img id="error-image" src="/web-gallery/v2/images/error.gif" />
					<p class="error-text">Please use the 'Back' button to get back to where you started.</p>
				</div>
			</div>
		</div>
		<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>
	</div>
	<div id="column2" class="column">
		<div class="habblet-container ">
			<div class="cbb clearfix green ">
				<h2 class="title">Were you looking for...</h2>
				<div id="notfound-looking-for" class="box-content">
					<p><b>A friend's group or personal page?</b><br/>
					See if it is listed on the <a href="/community">Community</a> page.</p>
					<p><b>Rooms that rock?</b><br/>
					Browse the <a href="/community">Recommended Rooms</a> list.</p>
					<p><b>What other {{ $shortname }}s are in to?</b><br/>
					Check out the <a href="/tag">Top Tags</a> list.</p>
					<p><b>How to get Coins?</b><br/>
					Have a look at the <a href="/credits">Coins</a> page.</p>
				</div>
			</div>
		</div>
		<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>
	</div>
	</div>
</div>
@endsection
