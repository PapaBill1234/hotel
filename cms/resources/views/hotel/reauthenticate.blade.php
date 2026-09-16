@extends('layouts.hotel-process')
@section('content')
<div id="column1" class="column">
	<div class="cbb clearfix green">
		<h2 class="title">Please enter your password</h2>
		<div class="box-content">
			<p>You need to enter your password to continue because you have signed in via 'remember-me'.</p>
			<p>If you are not <strong>{{ $hotelUser->username }}</strong>, please <a href="/account/logout?origin=default">sign out</a>.</p>
			<p>If you have forgotten your password, please <a href="/account/password/forgot">click here</a>.</p>
		</div>
	</div>
</div>
<div id="column2" class="column">
@if(!empty($error))
<div class="action-error flash-message"><div class="rounded"><ul>
	<li>{{ $error }}</li>
</ul></div></div>
@endif
<div class="cbb gray clearfix">
	<h2 class="title">Sign in</h2>
	<div class="box-content clearfix" id="login-habblet">
		<form action="/account/reauthenticate" method="post" class="login-habblet">
			@csrf
			<input type="hidden" name="page" value="{{ $nextPage }}" />
			<ul>
				<li>
					<label for="login-username" class="login-text">Username</label>
					<span class="username">{{ $hotelUser->username }}</span>
				</li>
				<li>
					<label for="login-password" class="login-text">Password</label>
					<input tabindex="2" type="password" class="login-field" name="password" id="login-password" />
					<input type="submit" value="Sign in" class="submit" id="login-submit-button"/>
					<a style="float: left; margin-left: 0pt; display: none" class="new-button" id="login-submit-new-button" href="#"><b style="padding-left: 10px; padding-right: 7px; width: 55px;">Sign in</b><i></i></a>
				</li>
			</ul>
		</form>
	</div>
</div>
</div>
<script type="text/javascript">
	if (typeof HabboView != "undefined") {
		HabboView.add(LoginFormUI.init);
		HabboView.add(RememberMeUI.init);
	}
</script>
@endsection
