@extends('layouts.hotel-process')
@section('content')
<style type="text/css">
div.left-column { float: left; width: 50% }
div.right-column { float: right; width: 49% }
label { display: block }
input { width: 98% }
input.process-button { width: auto; float: right }
</style>
<div class="left-column">
	<div class="cbb clearfix">
		<h2 class="title">Forgotten Your Password?</h2>
		<div class="box-content">
			@if(!empty($notice))
			<div class="rounded rounded-red">{{ $notice }}<br /></div>
			<div class="clear"></div>
			@endif
			<p>Don't panic! Please enter your account information below and we'll send you an email telling you how to reset your password.</p>
			<form method="post" action="/account/password/forgot">
				@csrf
				<p><label>Username</label><input name="forgottenpw-username" type="text" /></p>
				<p><label>Email address</label><input name="forgottenpw-email" type="text" /></p>
				<p><input type="submit" class="submit process-button" name="actionForgot" value="Request password email" /></p>
			</form>
		</div>
	</div>
</div>
<div class="right-column">
	<div class="cbb clearfix">
		<h2 class="title">Forgotten Your {{ $shortname }} Name?</h2>
		<div class="box-content">
			<p>No problem - just enter your email address below and we'll send you a list of your accounts.</p>
			<form method="post" action="/account/password/forgot">
				@csrf
				<p><label>Email address</label><input name="ownerEmailAddress" type="text" /></p>
				<p><input type="submit" class="submit process-button" name="actionList" value="Get my accounts" /></p>
			</form>
		</div>
	</div>
	<div class="cbb clearfix">
		<h2 class="title">False Alarm!</h2>
		<div class="box-content">
			<p>If you have remembered your password, or if you just came here by accident, click the link below to return to the Homepage.</p>
			<p><a href="/">Back to homepage &raquo;</a></p>
		</div>
	</div>
</div>
@endsection
