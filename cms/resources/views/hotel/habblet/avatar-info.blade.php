@if($row)
<div class="avatar-list-info-container">
	<div class="avatar-info-basic">
		<img src="{{ \App\Support\Hotel::avatarUrl($row->look ?? '', 'b,4,4,,1,0') }}" alt="" />
		<a href="/home/{{ $row->username }}">{{ $row->username }}</a>
		<p>{{ $row->motto }}</p>
	</div>
	<a href="#" class="avatar-list-info-close">Close</a>
</div>
@else
<p>Unknown user.</p>
@endif
