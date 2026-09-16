@extends('layouts.hotel-process')
@section('content')
<div id="process-content">
	<div id="email-verified-container">
		<div class="cbb clearfix {{ $ok ? 'green' : 'red' }}">
			<h2 class="title heading">{{ $ok ? 'Email link handled' : 'Error handling email link' }}</h2>
			<div class="box-content">
				<ul>
					<li>{{ $message }}</li>
				</ul>
				<a href="/">Continue to {{ $shortname }} front page.</a>
			</div>
		</div>
	</div>
</div>
@endsection
