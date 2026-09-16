<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>{{ $shortname }}: Page error </title>
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
<link rel="stylesheet" href="/web-gallery/v2/styles/habboclient.css" type="text/css" />
<script src="/web-gallery/static/js/habboclient.js" type="text/javascript"></script>
<script type="text/javascript">
document.habboLoggedIn = {{ !empty($hotelUser) ? 'true' : 'false' }};
var habboName = {!! !empty($hotelUser) ? json_encode($hotelUser->username) : 'null' !!};
var habboReqPath = "";
var habboStaticFilePath = "/web-gallery";
var habboImagerUrl = "/habbo-imaging/";
var habboPartner = "";
window.name = "client";
if (typeof HabboClient != "undefined") { HabboClient.windowName = "client"; }
</script>
<meta name="build" content="PHPRetro 4.0.10 BETA" />
</head>
<body id="popup" class="process-template client_error">
<div id="container">
	<div id="content">
		<div id="process-content" class="centered-client-error">
			<div id="column1" class="column">
				<div class="habblet-container ">
					<div class="cbb clearfix orange ">
						<h2 class="title">{{ $title }}</h2>
						<div class="box-content">
@if($key === 'install_shockwave')
							<div>
								<p>You need <b>Adobe Shockwave</b>.</p>
								<p>Nitro does not use Shockwave. Open the hotel client instead.</p>
							</div>
							<div id="swdetection"></div>
@elseif($key === 'upgrade_shockwave')
							<p>This browser does not use Shockwave. Open the hotel client instead.</p>
							<ul>
								<li class="client_error"><span>Close extra hotel windows.</span></li>
								<li class="client_error"><span>Open the hotel from Home.</span></li>
								<li class="client_error"><span>Use the Nitro client, not Flash.</span></li>
							</ul>
@elseif($key === 'error')
							<div class="info-client_error-text">
								<p>Oops. Something went wrong with the hotel client.</p>
								<p>Reopen <a onclick="openOrFocusHabbo(this); return false;" target="client" href="/client">the hotel</a> to continue.</p>
							</div>
							<div class="retry-enter-hotel">
								<div class="hotel-open">
									<a id="enter-hotel-open-image" class="open" href="/client" target="client" onclick="HabboClient.openOrFocus(this); return false;">
										<div class="hotel-open-image-splash"></div>
										<div class="hotel-image hotel-open-image"></div>
									</a>
									<div class="hotel-open-button-content">
										<a class="open" href="/client" target="client" onclick="HabboClient.openOrFocus(this); return false;">Enter</a>
										<span class="open"></span>
									</div>
								</div>
							</div>
@else
							<p>Connection failed. Hotel host and port come from emulator configuration, not this website.</p>
@endif
						</div>
					</div>
				</div>
				<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>
			</div>
			<script type="text/javascript">HabboView.run();</script>
			<div id="column2" class="column"></div>
		</div>
	</div>
</div>
</body>
</html>
