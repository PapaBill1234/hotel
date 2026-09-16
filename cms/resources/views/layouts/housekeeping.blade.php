<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>PHPRetro Housekeeping: {{ $pageName }}</title>
    <link rel="shortcut icon" href="/housekeeping/favicon.ico" type="image/vnd.microsoft.icon" />
<link rel="stylesheet" href="/housekeeping/images/styles/style.css" type="text/css">
<link rel="stylesheet" href="/housekeeping/images/styles/boxes.css" type="text/css">
<meta name="build" content="PHPRetro 4.0.10 BETA" />
</head>
<body>
<div class="panel">
<div class="header_left">&nbsp;<br />&nbsp;<br />&nbsp;<br /><a href="http://www.phpretro.com/"><img src="/housekeeping/images/header_logo.png" alt="PHPRetro"></a></div>
<div class="header_right"><img src="/housekeeping/images/header_tm1.gif"></div>
<div class="panel_title">
<span class="text">PHPRetro 4.0 Housekeeping</span>
<div class="close_button"><a href="/housekeeping/logout"><img src="/housekeeping/images/button_close.gif" alt="Logout"></a></div>
</div>
@if(($category ?? 'login') !== 'login')
<div class="panel_header">
	<ul class="{{ ($category ?? '')==='dashboard' ? 'selected' : '' }}" id="item">
	<li class="top"><div style="text-align:center"><a href="#">Dashboard</a></div></li>
	<li class="item"><a href="/housekeeping/dashboard">Home</a></li>
	<li class="item"><a href="/housekeeping/updates">Updates</a></li>
	<li class="item"><a href="/housekeeping/logs">Logs</a></li>
	<li class="item"><a href="/housekeeping/auditlog">Audit log</a></li>
	<li class="item"><a href="/housekeeping/about">About</a></li>
	</ul>
	<div class="border"></div>
	<ul class="{{ ($category ?? '')==='settings' ? 'selected' : '' }}" id="item">
	<li class="top"><div style="text-align:center"><a href="#">Settings</a></div></li>
	<li class="item"><a href="/housekeeping/settings">Site settings</a></li>
	<li class="item"><a href="/housekeeping/maintenance">Maintenance</a></li>
	<li class="item"><a href="/housekeeping/cache">Cache</a></li>
	<li class="item"><a href="/housekeeping/staffsessions">Staff sessions</a></li>
	<li class="item"><a href="/housekeeping/twofactor">Staff 2FA</a></li>
	</ul>
	<div class="border"></div>
	<ul class="{{ ($category ?? '')==='tools' ? 'selected' : '' }}" id="item">
	<li class="top"><div style="text-align:center"><a href="#">Tools</a></div></li>
	<li class="item"><a href="/housekeeping/banners">Banners</a></li>
	<li class="item"><a href="/housekeeping/campaigns">Campaigns</a></li>
	<li class="item"><a href="/housekeeping/catalogue">Catalogue</a></li>
	<li class="item"><a href="/housekeeping/collectables">Collectables</a></li>
	<li class="item"><a href="/housekeeping/faq">FAQ</a></li>
	<li class="item"><a href="/housekeeping/news">News</a></li>
	<li class="item"><a href="/housekeeping/newsletter">Newsletter</a></li>
	<li class="item"><a href="/housekeeping/recommended">Recommended</a></li>
	<li class="item"><a href="/housekeeping/vouchers">Vouchers</a></li>
	</ul>
	<div class="border"></div>
	<ul class="{{ ($category ?? '')==='users' ? 'selected' : '' }}" id="item">
	<li class="top"><div style="text-align:center"><a href="#">Users</a></div></li>
	<li class="item"><a href="/housekeeping/users">Users</a></li>
	<li class="item"><a href="/housekeeping/bans">Bans</a></li>
	<li class="item"><a href="/housekeeping/alerts">Alerts</a></li>
	<li class="item"><a href="/housekeeping/help">Help</a></li>
	<li class="item"><a href="/housekeeping/reports">Reports</a></li>
	<li class="item"><a href="/housekeeping/search">Search</a></li>
	</ul>
	<div class="border"></div>
</div>
<div class="clear"></div>
@endif
<div class="topborder"></div>
@yield('content')
<div class="page_footer">
<div class="buttons">
<input type="button" class="footer_button" value="Homepage" onclick="window.location.href='/'"></input>
</div>
</div>
<div class="copylight">Powered by <a href="http://www.phpretro.com/">PHPRetro</a><br />Housekeeping design &copy; 2009 <a href="http://www.ukumo.com/">xsixteen</a>, <a href="http://pixelarts.habbohack.servegame.org">Tsuka</a><br />HABBO is a registered trademark of Sulake Corporation. All rights reserved to their respective owner(s).</div>
</div>
</body>
</html>
