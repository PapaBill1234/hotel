<li class="guestbook-entry">
<div class="guestbook-author">
@if(!empty($hotelUser))
<img src="{{ \App\Support\Hotel::avatarUrl($hotelUser->look ?? '', 's,4,4,,1,0') }}" alt="{{ $hotelUser->username }}" />
@endif
</div>
<div class="guestbook-message">
<div class="offline">{{ $hotelUser->username ?? '' }}</div>
<p>{{ $message }}</p>
</div>
<div class="guestbook-cleaner">&nbsp;</div>
</li>
