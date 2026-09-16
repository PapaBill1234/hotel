@extends('layouts.hotel-community')
@section('content')
<div id="container"><div id="content" style="position: relative" class="clearfix">
<div id="column1" class="column"><div class="habblet-container"><div class="cbb clearfix default">
<h2 class="title">News</h2>
<div id="article-archive">
<ul>
@foreach($newsList as $item)
	<li><a href="/articles?id={{ $item->id }}" class="article-{{ $item->id }}">{{ $item->title }}&nbsp;&raquo;</a></li>
@endforeach
</ul>
<a href="/articles?archive=true">More news &raquo;</a>
</div></div></div></div>
<div id="column2" class="column"><div class="habblet-container"><div class="cbb clearfix notitle"><div id="article-wrapper">
@if($current)
<h2>{{ $current->title }}</h2>
<div class="article-meta">Posted {{ date('M j, Y', (int) $current->time) }}@if(!empty($current->categories)) — <a href="/articles?category={{ rawurlencode($current->categories) }}">{{ $current->categories }}</a>@endif</div>
<p class="summary">{!! nl2br(e($current->summary)) !!}</p>
<div class="article-body"><p>{!! nl2br(e($current->story ?? '')) !!}</p>
<div class="article-author">- {{ $current->author }}</div>
</div>
@else
<p>No news to display.</p>
@endif
</div></div></div></div>
</div></div>
@endsection
