<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{{ $shortname }} - Client</title>
<link rel="stylesheet" href="/web-gallery/v2/styles/style.css" type="text/css" />
<style>
html,body,#clientembed-container{margin:0;padding:0;height:100%;background:#000;color:#fff;font-family:Verdana,Arial,sans-serif;}
#client-topbar{background:#333;padding:6px 12px;color:#fff;}
#clientembed{padding:24px;text-align:center;}
</style>
</head>
<body id="client" class="wide">
<div id="client-topbar">
	<div class="habbocount">{{ $onlineCount }} {{ $shortname }}s online</div>
	<div class="logout"><a href="/account/logout?origin=popup">Close hotel</a></div>
</div>
<div id="clientembed-container">
	<div id="clientembed">
		@if($nitroUrl)
			<iframe src="{{ $nitroUrl }}" title="Nitro" style="width:100%;height:90vh;border:0"></iframe>
		@else
			<p>Hotel client chrome. Nitro host/port/assets are configuration, not CMS fields.</p>
			<p>SSO ticket written to <code>users.auth_ticket</code> only.</p>
			<p>Set <code>NITRO_CLIENT_URL</code> to embed the client.</p>
		@endif
	</div>
</div>
</body>
</html>
