<html>
<head>
	<title>Redirecting...</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<style type="text/css">body { background-color: #e3e3db; text-align: center; font: 11px Verdana, Arial, Helvetica, sans-serif; } a { color: #fc6204; }</style>
</head>
<body>
<script type="text/javascript">window.location.replace({!! json_encode($target) !!});</script>
<noscript><meta http-equiv="Refresh" content="0;URL={{ $target }}"></noscript>
<p class="btn">If you are not automatically redirected, please <a href="{{ $target }}" id="manual_redirect_link">click here</a></p>
</body>
</html>
