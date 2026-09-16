@extends('layouts.hotel-iot')
@section('content')
<div id="main-content-process">
	<div class="portlet">
		<div class="portlet-body-process">
			<div class="imaindiv">
				<h2>{{ $shortname }} Help Tool</h2>
				<p>Send a ticket to hotel staff. This website does not email PolarIS.</p>
				@if(!empty($error))
					<p class="error">{{ $error }}</p>
				@endif
				<form method="post" action="/iot/go">
					@csrf
					<p><label>Name</label><br><input name="username" maxlength="25" value="{{ old('username', $hotelUser?->username ?? '') }}"></p>
					<p><label>Email</label><br><input name="email" maxlength="255" value="{{ old('email', $hotelUser?->mail ?? '') }}"></p>
					<p><label>Subject</label><br><input name="subject" maxlength="50" value="{{ old('subject') }}"></p>
					<p><label>Message</label><br><textarea name="message" rows="8" cols="50">{{ old('message') }}</textarea></p>
					<p><button type="submit">Send</button></p>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
