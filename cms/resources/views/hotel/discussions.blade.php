@extends('layouts.hotel-community')
@section('head')
<link rel="stylesheet" href="/web-gallery/styles/myhabbo/myhabbo.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/v2/styles/group.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/styles/discussions.css" type="text/css" />
@endsection
@section('content')
<div id="container">
	<div id="content" style="position: relative" class="clearfix">
	<div id="mypage-wrapper" class="cbb blue">
<div class="box-tabs-container box-tabs-left clearfix">
	<h2 class="page-owner">{{ $guild->name }}</h2>
	<ul class="box-tabs">
		<li><a href="/groups/{{ (int) $guild->id }}/id">Front Page</a><span class="tab-spacer"></span></li>
		<li class="selected"><a href="/groups/{{ (int) $guild->id }}/id/discussions">Discussion Forum</a><span class="tab-spacer"></span></li>
	</ul>
</div>
<div id="mypage-content">
@if(!empty($forumNotice))<div class="box-content"><p>{{ $forumNotice }}</p></div>@endif
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="content-1col">
<tr>
<td valign="top" style="width: 750px;" class="habboPage-col rightmost">
<div id="discussionbox">
@if($thread)
<div id="group-postlist-container">
	<div class="postlist-header clearfix">
		<div class="page-num-list">View page: 1</div>
	</div>
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="group-postlist-list" id="group-postlist-list">
	@foreach($posts as $i => $post)
		@php $poster = $posters[$post->user_id] ?? null; $even = $i % 2 ? 'odd' : 'even'; @endphp
		<tr class="post-list-index-{{ $even }}">
			<td class="post-list-row-container">
				@if($poster)
				<a href="/home/{{ $poster->username }}" class="post-list-creator-link post-list-creator-info">{{ $poster->username }}</a>
				<img alt="offline" src="/web-gallery/images/myhabbo/habbo_offline.gif" />
				<div class="post-list-creator-avatar"><img src="{{ \App\Support\Hotel::avatarUrl($poster->look ?? '', 'b,2,2,,1,0') }}" alt="" /></div>
				@endif
			</td>
			<td class="post-list-message" valign="top" colspan="2">
				<span class="post-list-message-header">{{ $i === 0 ? $thread->subject : 'RE: '.$thread->subject }}</span><br />
				<span class="post-list-message-time">{{ date('M j, Y (g:i A)', (int) $post->created_at) }}</span>
				<div class="post-list-content-element">{!! nl2br(e($post->message)) !!}</div>
			</td>
		</tr>
	@endforeach
	</table>
	<p class="box-content">PolarIS forum posts are shown read-only. This website will not insert guilds_forums_comments.</p>
</div>
@else
<div id="group-topiclist-container">
<div class="topiclist-header clearfix">
	<div class="page-num-list">View page: {{ $threads->isEmpty() ? '0' : '1' }}</div>
</div>
<table class="group-topiclist" border="0" cellpadding="0" cellspacing="0" id="group-topiclist-list">
	<tr class="topiclist-columncaption">
		<td class="topiclist-columncaption-topic">Thread and first poster</td>
		<td class="topiclist-columncaption-lastpost">Last post</td>
		<td class="topiclist-columncaption-replies">Replies</td>
		<td class="topiclist-columncaption-views">Views</td>
	</tr>
	@forelse($threads as $i => $row)
	<tr class="topiclist-row-{{ $i % 2 ? 'odd' : 'even' }}">
		<td class="topiclist-rowtopic" valign="top">
			<div class="topiclist-row-content">
			<a class="topiclist-link {{ (int) $row->pinned === 1 ? 'icon icon-sticky' : '' }}" href="/groups/{{ (int) $guild->id }}/id/discussions/{{ (int) $row->id }}/id">{{ $row->subject }}</a>
			</div>
		</td>
		<td class="topiclist-rowlastpost">{{ date('M j, Y', (int) $row->updated_at) }}</td>
		<td class="topiclist-rowreplies">{{ max(0, (int) $row->posts_count - 1) }}</td>
		<td class="topiclist-rowviews">-</td>
	</tr>
	@empty
	<tr><td colspan="4" class="box-content">No threads.</td></tr>
	@endforelse
</table>
<p class="box-content">PolarIS has no per-thread view counters. New topics are not created from this website.</p>
</div>
@endif
</div>
</td>
</tr>
</table>
</div>
</div>
</div>
</div>
@endsection
