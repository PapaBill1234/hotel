@extends('layouts.hotel-community')
@section('content')
<div id="container">
	<div id="content" class="clearfix">
		<div id="column1" class="column">
			<div class="habblet-container ">
				<div class="cbb clearfix orange ">
					<h2 class="title">Tag Search</h2>
					<div id="tag-search-habblet-container" class="box-content">
						<form action="/tag" class="search-box">
							<input type="text" name="tag" id="search_query" value="{{ $tagQuery }}" />
							<button type="submit">Search</button>
						</form>
						<p>Tag search, tag clouds, matches, and fights are not available because PolarIS has no tags table and no project-owned replacement schema is defined.</p>
						@if($tagQuery !== '')
						<p class="search-result-count">0 users. 0 groups.</p>
						<p>No results found.</p>
						@endif
					</div>
				</div>
			</div>
			<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>
		</div>
		<div id="column2" class="column">
			<div class="habblet-container ">
				<div class="cbb clearfix green ">
					<h2 class="title">Popular Tags</h2>
					<div class="box-content">
						<p>No tags.</p>
					</div>
				</div>
			</div>
			<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>
			<div class="habblet-container ">
				<div class="cbb clearfix blue ">
					<h2 class="title">Tag Fight</h2>
					<div class="box-content">
						<p>The fight ... is on ...</p>
						<form action="/tag" method="get">
							<p>First Tag<br /><input name="tag1" disabled="disabled" /></p>
							<p>Second Tag<br /><input name="tag2" disabled="disabled" /></p>
							<p><button type="button" disabled="disabled">Fight</button></p>
						</form>
					</div>
				</div>
			</div>
			<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>
			<div class="habblet-container ">
				<div class="cbb clearfix red ">
					<h2 class="title">Tag Match</h2>
					<div class="box-content">
						<p>Type in the name of your friend to see how well you two match.</p>
						<form action="/tag" method="get">
							<input name="friend" disabled="disabled" />
							<button type="button" disabled="disabled">Match!</button>
						</form>
					</div>
				</div>
			</div>
			<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>
		</div>
	</div>
</div>
@endsection
