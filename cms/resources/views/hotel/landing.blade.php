@extends('layouts.hotel-process')
@section('content')
<div id="column1" class="column">
	<div class="habblet-container " id="create-habbo">
		<div id="create-habbo-nonflash" style="background-image: url(/web-gallery/v2/images/landing/landing_group.png)">
			<div id="landing-register-text"><a href="/register"><span>Join now, it's free &raquo;</span></a></div>
			<div id="landing-promotional-text"><span>{{ $shortname }} is a virtual world where you can meet and make friends.</span></div>
		</div>
	</div>
	<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>
</div>
<div id="column2" class="column">
	<div class="habblet-container ">
		@if(!empty($error))
		<div class="action-error flash-message">
			<div class="rounded">
				<ul>
					<li>{{ $error }}</li>
				</ul>
			</div>
		</div>
		@endif
		<div class="cbb loginbox clearfix">
			<h2 class="title">Sign in</h2>
			<div class="box-content clearfix" id="login-habblet">
				<form action="/account/submit" method="post" class="login-habblet">
					@csrf
					<ul>
						<li>
							<label for="login-username" class="login-text">Username</label>
							<input tabindex="1" type="text" class="login-field" name="username" id="login-username" value="{{ $username }}"/>
						</li>
						<li>
							<label for="login-password" class="login-text">Password</label>
							<input tabindex="2" type="password" class="login-field" name="password" id="login-password" />
							<input type="submit" value="Sign in" class="submit" id="login-submit-button"/>
							<a href="#" id="login-submit-new-button" class="new-button" style="float: left; margin-left: 0;display:none"><b style="padding-left: 10px; padding-right: 7px; width: 55px">Sign in</b><i></i></a>
						</li>
						<li class="no-label">
							<input tabindex="3" type="checkbox" value="true" name="_login_remember_me" id="login-remember-me"/>
							<label for="login-remember-me">Remember me</label>
						</li>
						<li class="no-label">
							<a href="/register" class="login-register-link"><span>Register for free</span></a>
						</li>
						<li class="no-label">
							<a href="/account/password/forgot" id="forgot-password"><span>I forgot my username/password</span></a>
						</li>
					</ul>
				</form>
			</div>
		</div>
	</div>
	<div class="habblet-container ">
		<div class="ad-container">
			<a href="/register"><img src="/web-gallery/v2/images/landing/uk_party_frontpage_image.gif" alt="" /></a>
		</div>
	</div>
</div>
<div id="column3" class="column"></div>
<div id="column-footer">
	<div class="habblet-container ">
		<div class="habblet box-content" id="tag-cloud-slim">
			<span class="tags-habbos-like">{{ $shortname }}s Like..</span>
		</div>
	</div>
</div>
@endsection
