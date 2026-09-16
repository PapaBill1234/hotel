@php
$online = ((string) ($entry->online ?? '0') !== '0') ? 'online' : 'offline';
$canDelete = !empty($hotelUser) && ((int) $hotelUser->id === (int) $entry->author_user_id || (int) $hotelUser->id === (int) ($entry->profile_user_id ?? 0));
@endphp
<li id="guestbook-entry-{{ (int) $entry->id }}" class="guestbook-entry">
<div class="guestbook-author">
<img src="{{ \App\Support\Hotel::avatarUrl($entry->look ?? '', 's,4,4,,1,0') }}" alt="{{ $entry->username }}" title="{{ $entry->username }}"/>
</div>
<div class="guestbook-actions">
@if($canDelete)
<img src="/web-gallery/images/myhabbo/buttons/delete_entry_button.gif" id="gbentry-delete-{{ (int) $entry->id }}" class="gbentry-delete" style="cursor:pointer" alt=""/>
@endif
</div>
<div class="guestbook-message">
<div class="{{ $online }}">
<a href="/home/{{ $entry->username }}">{{ $entry->username }}</a>
</div>
<p>{{ $entry->message }}</p>
</div>
<div class="guestbook-cleaner">&nbsp;</div>
<div class="guestbook-entry-footer metadata">{{ date('M j, Y g:i:s A', (int) $entry->created_at) }}</div>
</li>
