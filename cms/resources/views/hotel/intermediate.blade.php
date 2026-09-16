<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>{{ $shortname }}: Choose your destination </title>
<script type="text/javascript">var andSoItBegins = (new Date()).getTime();</script>
<link rel="shortcut icon" href="/web-gallery/v2/favicon.ico" type="image/vnd.microsoft.icon" />
<script src="/web-gallery/static/js/libs2.js" type="text/javascript"></script>
<script src="/web-gallery/static/js/visual.js" type="text/javascript"></script>
<script src="/web-gallery/static/js/libs.js" type="text/javascript"></script>
<script src="/web-gallery/static/js/common.js" type="text/javascript"></script>
<script src="/web-gallery/static/js/fullcontent.js" type="text/javascript"></script>
<link rel="stylesheet" href="/web-gallery/v2/styles/style.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/v2/styles/buttons.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/v2/styles/boxes.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/v2/styles/tooltips.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/v2/styles/process.css" type="text/css" />
<script type="text/javascript">
document.habboLoggedIn = false;
var habboName = null;
var habboReqPath = "";
var habboStaticFilePath = "/web-gallery";
var habboImagerUrl = "/habbo-imaging/";
var habboPartner = "";
var habboDefaultClientPopupUrl = "/client";
window.name = "habboMain";
if (typeof HabboClient != "undefined") { HabboClient.windowName = "client"; }
var timeoutID = null;
function timedRedirect() { timeoutID = setTimeout("location.href='/me'", 30000); }
function onClientOpen(f) {
	if (timeoutID != null) { clearTimeout(timeoutID); }
	if (typeof HabboClient != "undefined") { HabboClient.openOrFocus(f); }
	return false;
}
</script>
<meta name="build" content="PHPRetro 4.0.10 BETA" />
</head>
<body id="intermediate" class="process-template">
<div id="overlay"></div>
<div id="container">
	<div class="cbb process-template-box clearfix">
		<div id="content">
			<div id="header" class="clearfix">
				<h1><a href="/"></a></h1>
				<ul class="stats">
					<li class="stats-online"><span class="stats-fig">{{ $onlineCount }}</span> {{ $shortname }}s online now</li>
					<li class="stats-visited"><img src="/web-gallery/v2/images/{{ $hotelOnline ? 'online' : 'offline' }}.gif" alt="{{ $hotelOnline ? 'online' : 'offline' }}" border="0"></li>
				</ul>
			</div>
			<div id="process-content">
				<div id="enter-hotel">
					<div class="open enter-btn">
						<a href="/client" target="client" onclick="return onClientOpen(this)">Enter Hotel<i></i></a>
						<b></b>
					</div>
				</div>
				<div id="info">
					{{ $shortname }} is currently experiencing unexpectedly high load, if you just want to go to the Hotel, please click on the Enter Hotel button, and skip viewing the home page.
				</div>
				<div id="enter-mypage">
					<a href="/me">Go to Homepage</a>
				</div>
				<script type="text/javascript">timedRedirect();</script>
				@include('hotel.partials.footer-process')
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">if (typeof HabboView != "undefined") { HabboView.run(); }</script>
</body>
</html>
