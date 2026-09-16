@extends('layouts.hotel-community')
@section('head')
<link rel="stylesheet" href="/web-gallery/v2/styles/welcome.css" type="text/css" />
@endsection
@section('content')
<div id="container"><div id="content" class="clearfix">
<div id="column1" class="column">
	<div class="habblet-container">
		<div class="cbb clearfix lightgreen">
			<div class="welcome-intro clearfix">
				<img alt="{{ $hotelUser->username }}" src="{{ $avatar }}" width="64" height="110" class="welcome-habbo">
				<div id="welcome-intro-welcome-user">Welcome {{ $hotelUser->username }}!</div>
				<div class="box-content">{!! $welcomeText !!}</div>
			</div>
		</div>
	</div>
</div>
</div></div>
@endsection
