@extends('layouts.hotel-community')
@section('head')
<link rel="stylesheet" href="/web-gallery/v2/styles/rooms.css" type="text/css" />
<script src="/web-gallery/static/js/rooms.js" type="text/javascript"></script>
<script src="/web-gallery/static/js/moredata.js" type="text/javascript"></script>
@endsection
@section('content')
<div id="container">
	<div id="content" style="position: relative" class="clearfix">
    <div id="column1" class="column">

				<div class="habblet-container ">
						<div class="cbb clearfix green ">
<div class="box-tabs-container clearfix">
    <h2>Rooms</h2>
    <ul class="box-tabs">
        <li id="tab-0-0-1"><a href="#">Top Rated</a><span class="tab-spacer"></span></li>
        <li id="tab-0-0-2" class="selected"><a href="#">Recommended Rooms</a><span class="tab-spacer"></span></li>
    </ul>
</div>
    <div id="tab-0-0-1-content" style="display: none">
    		<div class="progressbar"><img src="/web-gallery/images/progress_bubbles.gif" alt="" width="29" height="6" /></div>
    </div>
    <div id="tab-0-0-2-content">
<div id="rooms-habblet-list-container-h119" class="recommendedrooms-lite-habblet-list-container">
        <ul class="habblet-list">
		@foreach($rooms as $i => $room)
		<li class="{{ $i % 2 ? 'odd' : 'even' }}">
    <span class="clearfix enter-room-link room-occupancy-1" title="Go to room">
	    <span class="room-enter">Enter</span>
	    <span class="room-name">{{ $room->name }}</span>
	    <span class="room-description"></span>
		<span class="room-owner">Owner: <a href="/home/{{ $room->owner_name }}">{{ $room->owner_name }}</a></span>
    </span>
</li>
		@endforeach
        </ul>
            <div class="clearfix">
                <a href="#" class="room-toggle-more-data" id="room-toggle-more-data-h119">Show more rooms</a>
            </div>
</div>
    </div>
					</div>
				</div>
				<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>

				<div class="habblet-container ">
						<div class="cbb clearfix blue ">
<div class="box-tabs-container clearfix">
    <h2>Groups</h2>
    <ul class="box-tabs">
        <li id="tab-0-1-1"><a href="#">Recent topics</a><span class="tab-spacer"></span></li>
        <li id="tab-0-1-2" class="selected"><a href="#">Hot Groups</a><span class="tab-spacer"></span></li>
    </ul>
</div>
<div class="box-content">
<div class="clearfix">
    <a href="#" class="discussions-toggle-more-data secondary" id="discussions-toggle-more-data-h121">Show more discussions</a>
</div>
</div>
					</div>
				</div>
				<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>

				<div class="habblet-container ">
						<div class="cbb clearfix activehomes ">
							<h2 class="title">Random {{ $shortname }}s - Click Us!</h2>
						<div id="homes-habblet-list-container" class="habblet-list-container">
	<img class="active-habbo-imagemap" src="/web-gallery/v2/images/activehomes/transparent_area.gif" width="435px" height="230px" usemap="#habbomap" />
@foreach($randomUsers as $i => $u)
        <div id="active-habbo-data-{{ $i }}" class="active-habbo-data">
                    <div class="active-habbo-data-container">
                        <div class="active-name offline">{{ $u->username }}</div>
                        Created on: {{ $u->account_day_of_birth ?? '' }}
                            <p class="moto">{{ $u->motto }}</p>
                    </div>
                </div>
                <input type="hidden" id="active-habbo-url-{{ $i }}" value="/home/{{ $u->username }}"/>
                <input type="hidden" id="active-habbo-image-{{ $i }}" class="active-habbo-image" value="{{ \App\Support\Hotel::avatarUrl($u->look ?? '', 'b,4,4,sml,1,0') }}" />
@endforeach
            <div id="placeholder-container">
				@for($i = 0; $i < 18; $i++)
                    <div id="active-habbo-image-placeholder-{{ $i }}" class="active-habbo-image-placeholder"></div>
				@endfor
            </div>
    </div>
    <map id="habbomap" name="habbomap">
            <area id="imagemap-area-0" shape="rect" coords="55,53,95,103" href="#" alt=""/>
            <area id="imagemap-area-1" shape="rect" coords="120,53,160,103" href="#" alt=""/>
            <area id="imagemap-area-2" shape="rect" coords="185,53,225,103" href="#" alt=""/>
            <area id="imagemap-area-3" shape="rect" coords="250,53,290,103" href="#" alt=""/>
            <area id="imagemap-area-4" shape="rect" coords="315,53,355,103" href="#" alt=""/>
            <area id="imagemap-area-5" shape="rect" coords="380,53,420,103" href="#" alt=""/>
            <area id="imagemap-area-6" shape="rect" coords="28,103,68,153" href="#" alt=""/>
            <area id="imagemap-area-7" shape="rect" coords="93,103,133,153" href="#" alt=""/>
            <area id="imagemap-area-8" shape="rect" coords="158,103,198,153" href="#" alt=""/>
            <area id="imagemap-area-9" shape="rect" coords="223,103,263,153" href="#" alt=""/>
            <area id="imagemap-area-10" shape="rect" coords="288,103,328,153" href="#" alt=""/>
            <area id="imagemap-area-11" shape="rect" coords="353,103,393,153" href="#" alt=""/>
            <area id="imagemap-area-12" shape="rect" coords="55,153,95,203" href="#" alt=""/>
            <area id="imagemap-area-13" shape="rect" coords="120,153,160,203" href="#" alt=""/>
            <area id="imagemap-area-14" shape="rect" coords="185,153,225,203" href="#" alt=""/>
            <area id="imagemap-area-15" shape="rect" coords="250,153,290,203" href="#" alt=""/>
            <area id="imagemap-area-16" shape="rect" coords="315,153,355,203" href="#" alt=""/>
            <area id="imagemap-area-17" shape="rect" coords="380,153,420,203" href="#" alt=""/>
    </map>
<script type="text/javascript">
    var activeHabbosHabblet = new ActiveHabbosHabblet();
    document.observe("dom:loaded", function() { activeHabbosHabblet.generateRandomImages(); });
</script>
					</div>
				</div>
				<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>

</div>
<div id="column2" class="column">
				<div class="habblet-container news-promo">
						<div class="cbb clearfix notitle ">
						<div id="newspromo">
        <div id="topstories">
	        <div class="topstory"@if(!empty($promo[0]->image)) style="background-image: url({{ $promo[0]->image }})"@endif>
	            <h4>Latest news</h4>
	            <h3>@if(!empty($promo[0]))<a href="/articles">{{ $promo[0]->title }}</a>@endif</h3>
	            <p class="summary">@if(!empty($promo[0])){{ $promo[0]->text }}@endif</p>
	            <p>
	                <a href="/articles">Read more</a>
	            </p>
	        </div>
            <div id="topstories-nav" style="display: none"><a href="#" class="prev">Previous</a><span>1</span> / 1<a href="#" class="next">Next</a></div>
        </div>
        <ul class="widelist">
            <li class="last"><a href="/articles">More news</a></li>
        </ul>
</div>
					</div>
				</div>
				<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>

				<div class="habblet-container ">
						<div class="cbb clearfix green ">
							<h2 class="title">Tags</h2>
						<div class="habblet box-content">
No tags to display.
    <div class="tag-search-form">
<form name="tag_search_form" action="/tag/search" class="search-box">
    <input type="text" name="tag" id="search_query" value="" class="search-box-query" style="float: left"/>
	<a onclick="$(this).up('form').submit(); return false;" href="#" class="new-button search-icon" style="float: left"><b><span></span></b><i></i></a>
</form>    </div>
</div>
					</div>
				</div>
				<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>
</div>
	</div>
</div>
@endsection
