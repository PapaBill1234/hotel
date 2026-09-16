@extends('layouts.hotel-community')
@section('content')
<div id="container"><div id="content" class="clearfix"><div id="column1" class="column">
<div class="habblet-container"><div class="cbb clearfix red">
<h2 class="title">{{ $shortname }} Club Test Wardrobe</h2>
<div class="box-content">
<p>Try on the club clothes for free here and then use the menu on the right to become a member and wear the clothes in the Hotel.</p>
@if(!empty($hotelUser))
<p>Your current figure: {{ $hotelUser->look }} ({{ $hotelUser->gender }})</p>
@else
<p>Please sign in first.</p>
@endif
<p><a href="/profile">Account Settings</a></p>
<p>Club clothes are worn in the hotel. This website does not grant PolarIS Club or write a wardrobe slot.</p>
</div>
</div></div>
</div>
<div id="column2" class="column">
<div class="habblet-container"><div class="cbb clearfix hcred">
<h2 class="title">What is {{ $shortname }} Club?</h2>
<div class="box-content">
<p><a href="/club">Read more</a></p>
</div>
</div></div>
</div>
</div></div>
@endsection
