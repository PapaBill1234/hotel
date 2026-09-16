<ul class="habblet-list">
@forelse($rows as $index => $row)
@php
	$ratio = (int) $row->users / max(1, (int) $row->users_max);
	$fill = $ratio >= .99 ? 5 : ($ratio > .65 ? 4 : ($ratio > .32 ? 3 : ($ratio > 0 ? 2 : 1)));
@endphp
<li class="{{ $index % 2 ? 'odd' : 'even' }} room-occupancy-{{ $fill }}" roomid="{{ (int) $row->room_id }}">
<div><span class="event-name"><a href="/client?forwardId=2&roomId={{ (int) $row->room_id }}">{{ $row->title }}</a></span>
<span class="event-owner"> by <a href="/home/{{ $row->owner_name }}">{{ $row->owner_name }}</a></span>
<p>{{ $row->description }} (<span class="event-date">{{ date('M j, Y H:i', (int) $row->start_timestamp) }}</span>)</p></div></li>
@empty
<li class="even">No events.</li>
@endforelse
</ul>
