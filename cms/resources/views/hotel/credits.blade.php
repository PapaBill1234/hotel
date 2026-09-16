@extends('layouts.hotel-community')
@section('content')
<div id="container">
	<div id="content" style="position: relative" class="clearfix">
	<div id="column1" class="column">
		<div class="habblet-container ">
			<div class="cbb clearfix green ">
				<h2 class="title">How to get Credits</h2>
				<div class="box-content">
					<p>Credits are the hotel currency. This website does not sell them.</p>
				</div>
			</div>
		</div>
	</div>
	<div id="column2" class="column">
		<div class="habblet-container ">
			<div class="cbb clearfix brown ">
				<h2 class="title">Your purse</h2>
				<div id="purse-habblet">
					@if(!empty($hotelUser))
					<ul>
						<li class="even icon-purse">
							<div>You have:</div>
							<span class="purse-balance-amount">{{ $hotelUser->credits }} Coins</span>
						</li>
						<li class="odd">
							<div class="box-content">
								<div>Redeem a Voucher!</div>
								<input type="text" value="" id="purse-habblet-redeemcode-string" class="redeemcode" disabled="disabled" />
								<p>This website cannot take coins. Redeem vouchers from the hotel catalog.</p>
								<p><a href="/client" class="new-button" target="client"><b>Open hotel</b><i></i></a></p>
							</div>
						</li>
					</ul>
					@else
					<div class="box-content">Please sign in to see your purse.</div>
					@endif
				</div>
			</div>
		</div>
	</div>
	</div>
</div>
@endsection
