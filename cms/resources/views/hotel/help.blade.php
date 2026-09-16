@extends('layouts.hotel-faq')
@section('content')
<div id="faq" class="clearfix">
<div id="faq-header" class="clearfix">
	<img src="/web-gallery/v2/images/faq/faq_header.png" alt="Help" />
	<form method="post" action="/help/faqsearch" class="search-box">
		@csrf
		<input type="text" id="faq-search" name="query" class="search-box-query search-box-onfocus" size="50" value="{{ $search !== '' ? $search : 'Search...' }}"/>
		<input type="submit" value="" title="Search" class="search" />
	</form>
</div>
<div id="faq-container" class="clearfix">
<div id="faq-category-list">
<ul class="faq">
@foreach($faqGroups as $category => $entries)
	<li><a href="/help/{{ (int) $entries[0]->id }}"><span class="faq-link {{ $selectedCategory === $category ? 'selected' : '' }}">{{ $category }}</span></a></li>
@endforeach
</ul>
</div>
<div id="faq-category-content">
<div id="faq-category-container">
@if(!empty($helpNotice))
	<div class="faq-item-content"><p>{{ $helpNotice }}</p></div>
@endif
@if($search !== '' && empty($faqGroups))
	<p>No matching FAQ found. Please search again.</p>
@endif
@php($items = $selectedCategory && isset($faqGroups[$selectedCategory]) ? $faqGroups[$selectedCategory] : [])
@forelse($items as $faq)
	<h4 class="faq-item-header" id="faq-header-text-{{ (int) $faq->id }}">
		<span class="faq-toggle">{{ $faq->question }}</span>
	</h4>
	<div class="faq-item-content" id="faq-item-content-{{ (int) $faq->id }}" style="display:none">
		<p>{!! nl2br(e($faq->answer)) !!}</p>
		<div class="faq-close-button"><img src="/web-gallery/v2/images/faq/close_btn.png" alt="Close FAQ" /></div>
	</div>
@empty
	<p>No FAQ entries are available yet.</p>
@endforelse
<div class="faq-need-help">
	<h3>Can't find an answer? Ask for help</h3>
	<p>Can't find the answer to your question? Send a ticket to hotel staff. This website does not email PolarIS.</p>
	<form method="post" action="/iot/go">
		@csrf
		<p><label>Email</label><br /><input name="email" maxlength="255" value="{{ old('email', $hotelUser?->mail ?? '') }}" /></p>
		<p><label>Subject</label><br /><input name="subject" maxlength="50" value="{{ old('subject') }}" /></p>
		<p><label>Message</label><br /><textarea name="message" rows="6" cols="40">{{ old('message') }}</textarea></p>
		<p><button type="submit">Send</button></p>
	</form>
</div>
</div>
</div>
</div>
</div>
<script type="text/javascript">if (typeof FaqItems != "undefined") { FaqItems.init(); }</script>
@endsection
