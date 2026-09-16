<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>{{ $shortname }}: {{ $pageName ?? 'Help' }} </title>
<script type="text/javascript">var andSoItBegins = (new Date()).getTime();</script>
<link rel="shortcut icon" href="/web-gallery/v2/favicon.ico" type="image/vnd.microsoft.icon" />
<script src="/web-gallery/static/js/visual.js" type="text/javascript"></script>
<script src="/web-gallery/static/js/libs.js" type="text/javascript"></script>
<script src="/web-gallery/static/js/common.js" type="text/javascript"></script>
<script src="/web-gallery/static/js/fullcontent.js" type="text/javascript"></script>
<script src="/web-gallery/static/js/libs2.js" type="text/javascript"></script>
<link rel="stylesheet" href="/web-gallery/v2/styles/style.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/v2/styles/buttons.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/v2/styles/boxes.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/v2/styles/tooltips.css" type="text/css" />
<script type="text/javascript">
document.habboLoggedIn = {{ !empty($hotelUser) ? 'true' : 'false' }};
var habboName = {!! !empty($hotelUser) ? json_encode($hotelUser->username) : 'null' !!};
var habboReqPath = "";
var habboStaticFilePath = "/web-gallery";
var habboImagerUrl = "/habbo-imaging/";
var habboPartner = "";
window.name = "habboMain";
</script>
<meta name="build" content="PHPRetro 4.0.10 BETA" />
</head>
<body id="faq" class="plain-template">
<script src="/web-gallery/static/js/faq.js" type="text/javascript"></script>
@yield('content')
<div id="faq-footer" class="clearfix">
	<p><a href="/papers/disclaimer" target="_self">Disclaimer</a> | <a href="/papers/privacy" target="_self">Privacy Policy</a>{!! $faqFooter ?? '' !!}</p>
	<p>Powered by <a href="http://www.phpretro.com/">PHPRetro</a><br />HABBO is a registered trademark of Sulake Corporation. All rights reserved to their respective owner(s).</p>
</div>
<script type="text/javascript">if (typeof HabboView != "undefined") { HabboView.run(); }</script>
</body>
</html>
