@forelse($entries as $entry)
	@include('hotel.habblet.guestbook-entry', ['entry' => $entry, 'hotelUser' => $hotelUser])
@empty
<div id="guestbook-empty-notes">This guestbook has no entries.</div>
@endforelse
