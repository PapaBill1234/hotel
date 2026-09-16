@extends('layouts.hotel-process')
@section('content')
<div id="column1" class="column">
	<div class="habblet-container ">
		<div class="cbb clearfix green">
			<h2 class="title">Register now!</h2>
			<div class="box-content">
				<p>Need an account?</p>
				<div class="register-button clearfix">
					<a href="/register" onclick="if(typeof HabboClient!='undefined'){HabboClient.closeHabboAndOpenMainWindow(this);} return false;">Create one</a>
					<span></span>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>
</div>
<div id="column2" class="column">
	<div class="habblet-container ">
		<div class="cbb loginbox clearfix ">
			<h2 class="title">Sign in</h2>
			<div class="box-content clearfix" id="login-habblet">
				@if(!empty($error))<p class="login-error">{{ $error }}</p>@endif
				<form action="/account/submit" method="post" class="login-habblet">
					@csrf
					<input type="hidden" name="page" value="/client" />
					<ul>
						<li>
							<label for="login-username" class="login-text">Username</label>
							<input tabindex="1" type="text" class="login-field" name="username" id="login-username" value="{{ $username }}" />
						</li>
						<li>
							<label for="login-password" class="login-text">Password</label>
							<input tabindex="2" type="password" class="login-field" name="password" id="login-password" />
						</li>
					</ul>
					<div class="clear"></div>
					<div class="login-submit">
						<input type="submit" value="Sign in" class="submit" />
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
