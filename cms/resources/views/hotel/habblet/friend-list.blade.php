<ul class="avatar-widget-list">
@forelse($friends as $row)
	<li class="clearfix">
		<img src="{{ \App\Support\Hotel::avatarUrl($row->look ?? '', 's,2,2,sml,1,0') }}" alt="" />
		<a href="/home/{{ $row->username }}">{{ $row->username }}</a>
	</li>
@empty
	<li>No friends.</li>
@endforelse
</ul>
