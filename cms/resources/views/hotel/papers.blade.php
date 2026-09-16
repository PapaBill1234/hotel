@extends('layouts.hotel-community')
@section('content')
<div id="container">
	<div id="content" class="clearfix">
		<div id="column1" class="column">
			<div class="habblet-container ">
				<div class="cbb clearfix blue ">
					<h2 class="title">{{ $paperTitle }}</h2>
					<div class="box-content hotel-article">
						{!! $paperBody !!}
					</div>
				</div>
			</div>
			<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>
		</div>
		<div id="column2" class="column">
			<div class="habblet-container ">
				<div class="cbb clearfix blue ">
					<h2 class="title">Hotel Policy</h2>
					<div class="box-content">
						{!! $hotelPolicy !!}
					</div>
				</div>
			</div>
			<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>
		</div>
	</div>
</div>
@endsection
