@extends('layouts.hotel-community')
@section('content')
<div id="container">
	<div id="content" style="position: relative" class="clearfix">
	<div id="column1" class="column">
		<div class="habblet-container ">
			<div class="cbb clearfix hcred ">
				<h2 class="title">{{ $shortname }} Club: become a VIP!</h2>
				<div id="habboclub-products">
					<div id="habboclub-clothes-container">
						<div class="habboclub-extra-image"></div>
						<div class="habboclub-clothes-image"></div>
					</div>
					<div class="clearfix"></div>
					<div id="habboclub-furniture-container">
						<div class="habboclub-furniture-image"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="habblet-container ">
			<div class="cbb clearfix lightbrown ">
				<h2 class="title">Benefits</h2>
				<div id="habboclub-info" class="box-content">
					<p>{{ $shortname }} Club is our VIP members-only club - absolutely no riff-raff admitted! Members enjoy a wide range of benefits, including exclusive clothes, free gifts and an extended Friends List.</p>
					<h3 class="heading">1. Extra Clothes & Accessories</h3>
					<p class="content habboclub-clothing">Show off your new status with a variety of extra clothes and accessories, along with special hairstyles and colors.<br /><br /><a href="/credits/club/tryout">Try out {{ $shortname }} Club clothes for yourself!</a></p>
				</div>
			</div>
		</div>
	</div>
	<div id="column2" class="column">
		<div class="habblet-container ">
			<div class="cbb clearfix hcred ">
				<h2 class="title">My Membership</h2>
				<div id="hc-habblet">
					<div id="hc-membership-info" class="box-content">
						<p>You are not a member of {{ $shortname }} Club</p>
					</div>
					<div id="hc-buy-container" class="box-content">
						<div id="hc-buy-buttons" class="hc-buy-buttons rounded rounded-hcred">
							<p>This website cannot take coins or grant club membership. Buy it from the hotel catalog.</p>
							<p><a href="/client" class="new-button" target="client"><b>Open hotel</b><i></i></a></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
</div>
@endsection
