@extends('layouts.hotel-community')
@section('head')
<script src="/web-gallery/static/js/settings.js" type="text/javascript"></script>
<link rel="stylesheet" href="/web-gallery/v2/styles/settings.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/v2/styles/friendmanagement.css" type="text/css" />
@endsection
@section('content')
<div id="container">
	<div id="content" class="clearfix">
	<div id="column1" class="column">
		<div class="habblet-container ">
			<div class="cbb clearfix settings ">
				<h2 class="title">Account settings</h2>
				<div class="box-content">
					@if(!empty($notice))
					<div class="rounded rounded-green">{{ $notice }}</div>
					@endif
					<p>These fields write PolarIS <code>users.motto</code>, <code>users.look</code>, and <code>users.gender</code> only.</p>
					<form method="post" action="/profile">
						@csrf
						<p>
							<label for="motto">Motto</label><br />
							<input id="motto" name="motto" maxlength="127" value="{{ $hotelUser->motto }}" />
						</p>
						<p>
							<label for="look">Figure</label><br />
							<input id="look" name="look" maxlength="256" value="{{ $hotelUser->look }}" />
						</p>
						<p>
							<label for="gender">Gender</label><br />
							<select id="gender" name="gender">
								<option value="M" @selected($hotelUser->gender==='M')>M</option>
								<option value="F" @selected($hotelUser->gender==='F')>F</option>
							</select>
						</p>
						<p><input type="submit" value="Save" class="submit" /></p>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div id="column2" class="column">
		<div class="habblet-container ">
			<div class="cbb clearfix default ">
				<h2 class="title">Your account</h2>
				<div class="box-content">
					<p><strong>{{ $hotelUser->username }}</strong></p>
					<p>{{ $hotelUser->mail }}</p>
				</div>
			</div>
		</div>
	</div>
	</div>
</div>
@endsection
