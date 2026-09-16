<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>{{ $shortname }}: {{ $pageName ?? '' }} </title>
<script type="text/javascript">var andSoItBegins = (new Date()).getTime();</script>
<link rel="shortcut icon" href="/web-gallery/v2/favicon.ico" type="image/vnd.microsoft.icon" />
<link rel="alternate" type="application/rss+xml" title="{{ $shortname }}: RSS" href="/articles/rss.xml" />
<script src="/web-gallery/static/js/libs2.js" type="text/javascript"></script>
<script src="/web-gallery/static/js/visual.js" type="text/javascript"></script>
<script src="/web-gallery/static/js/libs.js" type="text/javascript"></script>
<script src="/web-gallery/static/js/common.js" type="text/javascript"></script>
<script src="/web-gallery/static/js/fullcontent.js" type="text/javascript"></script>
<link rel="stylesheet" href="/web-gallery/v2/styles/style.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/v2/styles/buttons.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/v2/styles/boxes.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/v2/styles/tooltips.css" type="text/css" />
@yield('head')
<script type="text/javascript">
document.habboLoggedIn = {{ !empty($hotelUser) ? 'true' : 'false' }};
var habboName = {!! !empty($hotelUser) ? json_encode($hotelUser->username) : 'null' !!};
var ad_keywords = "";
var habboReqPath = "";
var habboStaticFilePath = "/web-gallery";
var habboImagerUrl = "/habbo-imaging/";
var habboPartner = "";
window.name = "habboMain";
if (typeof HabboClient != "undefined") { HabboClient.windowName = "client"; }
if (typeof L10N != "undefined") { L10N.put("purchase.group.title", "Create a Group"); }
</script>
<meta name="build" content="PHPRetro 4.0.10 BETA" />
</head>
<body id="{{ $bodyId ?? 'home' }}" class="{{ empty($hotelUser) ? 'anonymous' : '' }}">
<div id="overlay"></div>
<div id="header-container">
	<div id="header" class="clearfix">
		<h1><a href="/"></a></h1>
		<div id="subnavi">
		@if(!empty($hotelUser))
			<div id="subnavi-user">
				<ul>
					<li id="myfriends"><a href="#"><span>My Friends</span></a><span class="r"></span></li>
					<li id="mygroups"><a href="#"><span>My Groups</span></a><span class="r"></span></li>
					<li id="myrooms"><a href="#"><span>My Rooms</span></a><span class="r"></span></li>
				</ul>
			</div>
			<div id="subnavi-search">
				<div id="subnavi-search-upper">
				<ul id="subnavi-search-links">
					<li><a href="/help">Help</a></li>
					<li><a href="/account/logout" class="userlink" id="signout">Sign Out</a></li>
				</ul>
				</div>
			</div>
			<div id="to-hotel">
				@if($hotelOnline)
					<a href="/client" class="new-button green-button" target="client" onclick="if(typeof HabboClient!='undefined'){HabboClient.openOrFocus(this);} return false;"><b>Enter {{ $shortname }}</b><i></i></a>
				@else
					<div id="hotel-closed-medium">Closed</div>
				@endif
			</div>
		@else
			<div id="subnavi-user">
				<div class="clearfix">&nbsp;</div>
				<p><a href="/client" id="enter-hotel-open-medium-link" target="client">Enter {{ $shortname }}</a></p>
			</div>
		@endif
		</div>
		<ul id="navi">
			@if(!empty($hotelUser))
			<li class="{{ ($cat ?? '') === 'home' ? 'selected' : '' }}">
				@if(($cat ?? '') === 'home')
					<strong>{{ $hotelUser->username }} </strong>
				@else
					<a href="/me">{{ $hotelUser->username }}</a>
				@endif
				<span></span>
			</li>
			@else
			<li id="tab-register-now"><a href="/register">Register now!</a><span></span></li>
			@endif
			<li class="{{ ($cat ?? '') === 'community' ? 'selected' : '' }}">
				@if(($cat ?? '') === 'community')
					<strong>Community </strong>
				@else
					<a href="/community">Community</a>
				@endif
				<span></span>
			</li>
			<li class="{{ ($cat ?? '') === 'credits' ? 'selected' : '' }}">
				@if(($cat ?? '') === 'credits')
					<strong>Coins </strong>
				@else
					<a href="/credits">Coins</a>
				@endif
				<span></span>
			</li>
			@if(!empty($hotelUser) && $hotelUser->isStaff())
			<li id="tab-register-now"><a href="/housekeeping/">Housekeeping</a><span></span></li>
			@endif
		</ul>
		<div id="habbos-online"><div class="rounded"><span>{{ $onlineCount }} {{ $shortname }}s online</span></div></div>
	</div>
</div>
<div id="content-container">
@if(($cat ?? '') === 'home' && !empty($hotelUser))
<div id="navi2-container" class="pngbg">
	<div id="navi2" class="pngbg clearfix">
		<ul>
			<li class="{{ ($pageId ?? '') === 'me' ? 'selected' : '' }}">
				@if(($pageId ?? '') === 'me')
					Home
				@else
					<a href="/me">Home</a>
				@endif
			</li>
			<li class="{{ ($pageId ?? '') === 'home' ? 'selected' : '' }}"><a href="/home/{{ $hotelUser->username }}">My Page</a></li>
			<li class="{{ ($pageId ?? '') === 'profile' ? 'selected' : '' }}">
				@if(($pageId ?? '') === 'profile')
					Account Settings
				@else
					<a href="/profile">Account Settings</a>
				@endif
			</li>
			<li class=" last"><a href="/club">{{ $shortname }} Club</a></li>
		</ul>
	</div>
</div>
@elseif(($cat ?? '') === 'community')
<div id="navi2-container" class="pngbg">
	<div id="navi2" class="pngbg clearfix">
		<ul>
			<li class="{{ ($pageId ?? '') === 'community' ? 'selected' : '' }}">
				@if(($pageId ?? '') === 'community')
					Community
				@else
					<a href="/community">Community</a>
				@endif
			</li>
			<li class="{{ ($pageId ?? '') === 'news' ? 'selected' : '' }}">
				@if(($pageId ?? '') === 'news')
					News
				@else
					<a href="/articles">News</a>
				@endif
			</li>
			<li class="{{ ($pageId ?? '') === 'tags' ? 'selected' : '' }} last">
				@if(($pageId ?? '') === 'tags')
					Tags
				@else
					<a href="/tag">Tags</a>
				@endif
			</li>
		</ul>
	</div>
</div>
@elseif(($cat ?? '') === 'credits')
<div id="navi2-container" class="pngbg">
	<div id="navi2" class="pngbg clearfix">
		<ul>
			<li class="{{ ($pageId ?? '') === 'credits' ? 'selected' : '' }}">
				@if(($pageId ?? '') === 'credits')
					Coins
				@else
					<a href="/credits">Coins</a>
				@endif
			</li>
			<li class="{{ ($pageId ?? '') === 'club' ? 'selected' : '' }}">
				@if(($pageId ?? '') === 'club')
					{{ $shortname }} Club
				@else
					<a href="/club">{{ $shortname }} Club</a>
				@endif
			</li>
			<li class="{{ ($pageId ?? '') === 'collectables' ? 'selected' : '' }}">
				@if(($pageId ?? '') === 'collectables')
					Collectables
				@else
					<a href="/credits/collectables">Collectables</a>
				@endif
			</li>
			<li class="{{ ($pageId ?? '') === 'pixels' ? 'selected' : '' }}">
				@if(($pageId ?? '') === 'pixels')
					Pixels
				@else
					<a href="/credits/pixels">Pixels</a>
				@endif
			</li>
			<li class="{{ ($pageId ?? '') === 'credits' && ($pageName ?? '') === 'Transaction history' ? 'selected' : '' }} last">
				@if(($pageName ?? '') === 'Transaction history')
					History
				@else
					<a href="/credits/history">History</a>
				@endif
			</li>
		</ul>
	</div>
</div>
@endif
@yield('content')
<div id="column3" class="column">
	<div class="habblet-container "><div class="ad-container"></div></div>
</div>
</div>
<div id="footer">
	<p><a href="/" target="_self">Homepage</a> | <a href="/papers/disclaimer" target="_self">Disclaimer</a> | <a href="/papers/privacy" target="_self">Privacy Policy</a>{!! $faqFooter ?? '' !!}</p>
	<p>Powered by <a href="http://www.phpretro.com/">PHPRetro</a><br />HABBO is a registered trademark of Sulake Corporation. All rights reserved to their respective owner(s).</p>
</div>
<script type="text/javascript">if (typeof HabboView != "undefined") { HabboView.run(); }</script>
</body>
</html>
