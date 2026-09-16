@if($guild)
<h4>{{ $guild->name }}</h4>
<p>{!! nl2br(e($guild->description)) !!}</p>
<p><a href="/groups/{{ (int) $guild->id }}/id">Open group page</a></p>
@else
<p>Unknown group.</p>
@endif
