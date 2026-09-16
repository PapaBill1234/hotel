<ul class="avatar-widget-list">
@forelse($members as $row)
	<li><a href="/home/{{ $row->username }}">{{ $row->username }}</a>@if((int) $row->level_id === 0) (owner)@elseif((int) $row->level_id === 1) (admin)@endif</li>
@empty
	<li>No members.</li>
@endforelse
</ul>
