@extends('layouts.hotel-community')
@section('head')
<link rel="stylesheet" href="/web-gallery/v2/styles/personal.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/v2/styles/minimail.css" type="text/css" />
<script type="text/javascript">if (typeof L10N != "undefined") { L10N.put("purchase.group.title", "Create a Group"); }</script>
@endsection
@section('content')
<div id="container">
	<div id="content">
	<div id="column1" class="column">
		<div class="habblet-container ">
			<div id="new-personal-info" style="background-image:url(/web-gallery/v2/images/personal_info/hotel_views/{{ $hotelView }})">
				<div class="enter-hotel-btn">
					<div class="open enter-btn">
						<a href="/client" target="client" onclick="if(typeof openOrFocusHabbo=='function'){openOrFocusHabbo(this);} return false;">Enter {{ $shortname }}<i></i></a>
						<b></b>
					</div>
				</div>
				<div id="habbo-plate">
					<a href="/profile"><img alt="{{ $hotelUser->username }}" src="{{ $avatar }}" width="64" height="110" /></a>
				</div>
				<div id="habbo-info">
					<div id="motto-container" class="clearfix">
						<strong>{{ $hotelUser->username }}:</strong>
						<div><span>{{ $hotelUser->motto !== '' ? $hotelUser->motto : 'Click here to enter your motto' }}</span></div>
					</div>
				</div>
				<ul id="link-bar" class="clearfix">
					<li class="change-looks"><a href="/profile">Change looks &raquo;</a></li>
					<li class="credits"><a href="/credits">{{ $hotelUser->credits }}</a> Credits</li>
					<li class="club"><a href="/club">Join {{ $shortname }} club &raquo;</a></li>
					<li class="activitypoints"><a href="/credits/pixels">{{ $hotelUser->pixels }}</a> Pixels</li>
				</ul>
				<div id="habbo-feed">
					<ul id="feed-items">
						<li class="small" id="feed-lastlogin">Last signed in: {{ $hotelUser->lastLogin ? date('M j, Y g:i:s A', $hotelUser->lastLogin) : 'Never' }}</li>
					</ul>
				</div>
				<p class="last"></p>
			</div>
		</div>
		<div class="habblet-container ">
			<div class="cbb clearfix orange ">
				<h2 class="title">Hot Campaigns</h2>
				<div id="hotcampaigns-habblet-list-container">
					<ul id="hotcampaigns-habblet-list">
						@forelse($campaigns as $i => $campaign)
						<li class="{{ $i % 2 ? 'even' : 'odd' }}">
							<div class="hotcampaign-container">
								<h3>{{ $campaign->name }}</h3>
								<p>{{ $campaign->desc }}</p>
							</div>
						</li>
						@empty
						@endforelse
					</ul>
				</div>
			</div>
		</div>
		<div class="habblet-container minimail" id="mail">
			<div class="cbb clearfix blue ">
				<h2 class="title">My Messages</h2>
				<div id="minimail">
					<div class="minimail-contents" id="minimail-contents">
						<p class="empty">Loading messages…</p>
					</div>
					<script type="text/javascript">
					new Ajax.Updater("minimail-contents", "/minimail/loadMessages", {method:"post", parameters:{label:"inbox"}, evalScripts:true});
					</script>
				</div>
			</div>
		</div>
	</div>
	<div id="column2" class="column">
		<div class="habblet-container news-promo">
			<div class="cbb clearfix notitle ">
				<div id="newspromo">
					<div id="topstories">
						<div class="topstory">
							<h4>Latest news</h4>
							<h3>@if(!empty($news[0]))<a href="/articles?id={{ $news[0]->id }}">{{ $news[0]->title }}</a>@endif</h3>
							<p class="summary">@if(!empty($news[0])){{ $news[0]->summary }}@endif</p>
							<p>@if(!empty($news[0]))<a href="/articles?id={{ $news[0]->id }}">Read more</a>@endif</p>
						</div>
					</div>
					<ul class="widelist">
						<li class="last"><a href="/articles">More news</a></li>
					</ul>
				</div>
			</div>
		</div>
		<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>
		<div class="habblet-container ">
			<div class="cbb clearfix default ">
				<div class="box-tabs-container clearfix">
					<h2>Staff Picks</h2>
					<ul class="box-tabs">
						<li class="selected"><a href="#">Groups</a><span class="tab-spacer"></span></li>
						<li><a href="#">Rooms</a><span class="tab-spacer"></span></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="habblet-container ">
			<div class="cbb clearfix green ">
				<h2 class="title">Tags</h2>
				<div class="box-content">No tags to display.</div>
			</div>
		</div>
		<div class="habblet-container ">
			<div class="cbb clearfix blue ">
				<div class="box-tabs-container clearfix">
					<h2>Groups</h2>
					<ul class="box-tabs">
						<li class="selected"><a href="#">My Groups</a><span class="tab-spacer"></span></li>
						<li><a href="#">Hot Groups</a><span class="tab-spacer"></span></li>
					</ul>
				</div>
				<div class="box-content">
					<p>View the groups you are in, create your own group, or get some inspiration from the 'Hot Groups'-tab!</p>
					<p><a href="#" class="new-button" onclick="if(typeof GroupPurchase!='undefined'){GroupPurchase.open();} return false;"><b>Create/buy a Group</b><i></i></a></p>
				</div>
			</div>
		</div>
	</div>
	</div>
</div>
@endsection
