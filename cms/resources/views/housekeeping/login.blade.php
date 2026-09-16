@extends('layouts.housekeeping')
@section('content')
<div class="page_title">
 <img src="/housekeeping/images/icons/cfh.gif" class="pticon">
 <span class="page_name_shadow">Login</span>
 <span class="page_name">Login</span>
</div>
<div class="page_main">
<table border="0" cellpadding="0" cellspacing="0" height="100%">
<tbody>
<tr height="100%">
<td class="page_main_left">
<div class="left_date">{{ date('l F j, Y | g:iA') }}</div>
<div class="hr"></div>
<div class="loginuser">Please log in</div>
<div class="text">
<form id="loginform" action="/housekeeping/" method="post">
@csrf
<strong>Username:</strong><br />
<input type="text" size="20" name="username" id="namefield" value="{{ $username }}" /><br />
<strong>Password:</strong><br />
<input type="password" size="20" name="password" value="" /><br />
<strong>Authenticator code (staff):</strong><br />
<input type="text" size="20" name="totp_code" inputmode="numeric" maxlength="6" value="" />
<div class="button left"><input type="submit" value="Submit"></input></div>
</form>
</div>
<div class="hr"></div>
<div class="text">
If you have forgot your password, please use the <a href="/account/password/forgot">recovery tool</a> or contact your system administrator.
</div>
 </td>
 <td class="page_main_right">
@if(!empty($error))
	<div class="center">
		<div class="clean-error">{{ $error }}</div>
	</div>
@endif
  <div class="login_top">
  <img src="/housekeeping/images/workman_habbo_down.gif" /><br />
  PHPRetro Version 4.0.10 BETA
  </div>
 </td>
</tr>
</tbody>
</table>
</div>
@endsection
