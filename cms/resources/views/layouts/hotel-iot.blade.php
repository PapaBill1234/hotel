<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <meta http-equiv="content-type" content="text/html;charset=utf-8" />
 <title>{{ $pageName ?? 'Help Tool' }}</title>
 <link href="/web-gallery/content3/styles/style.css" type="text/css" rel="stylesheet"/>
 <link href="/web-gallery/content3/styles/style-iot.css" type="text/css" rel="stylesheet"/>
</head>
<body>
 <div>
  <table border="0" cellpadding="0" cellspacing="0" width="720">
   <tr>
    <td style="background: url(/web-gallery/content3/images/process/top_left.gif) no-repeat; width: 8px; height: 77px;">&nbsp;</td>
    <td style="background: url(/web-gallery/content3/images/process/top_mid.gif) repeat-x;" valign="top"><div style="margin: 0; padding: 10px 0 0 27px; height: 67px;"><img src="/web-gallery/v2/images/habbo.png"/></div></td>
    <td style="background: url(/web-gallery/content3/images/process/top_header_left.gif) no-repeat; width: 3px; height: 77px;"></td>
    <td style="background: url(/web-gallery/content3/images/process/top_header_mid.gif) repeat-x; height: 77px;"><div style="height: 43px; padding: 31px 0 0 4px; margin: 0; color: #fff; text-transform: uppercase; font-weight: bold; display: block;">{{ $shortname }} Help Tool</div></td>
    <td style="background: url(/web-gallery/content3/images/process/top_right.gif) no-repeat; width: 26px; height: 77px;">&nbsp;</td>
   </tr>
  </table>
 </div>
@yield('content')
</body>
</html>
