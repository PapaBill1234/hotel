@php
$emptyCopy = match ($label) {
    'sent' => 'No sent messages.',
    'trash' => 'No deleted messages.',
    'conversation' => 'No conversation messages.',
    default => !empty($unreadOnly) ? 'No unread messages.' : 'No messages.',
};
$startnum = $total === 0 ? 0 : $offset + 1;
$endnum = min($offset + 10, $total);
$nav = $total === 0 ? '' : $startnum.' - '.$endnum.' of '.$total;
$inboxClass = $label === 'inbox' ? 'selected' : '';
$sentClass = $label === 'sent' ? 'selected' : '';
$trashClass = $label === 'trash' ? 'selected' : '';
$unreadLabel = $unread > 0 ? ' ('.$unread.')' : '';
@endphp
<a href="#" class="new-button compose"><b>Compose</b><i></i></a>
<div class="clearfix labels nostandard">
<ul class="box-tabs">
<li class="{{ $inboxClass }}"><a href="#" label="inbox">Inbox{{ $unreadLabel }}</a><span class="tab-spacer"></span></li>
<li class="{{ $sentClass }}"><a href="#" label="sent">Sent</a><span class="tab-spacer"></span></li>
<li class="{{ $trashClass }}"><a href="#" label="trash">Trash</a><span class="tab-spacer"></span></li>
</ul>
</div>
<div id="message-list" class="label-{{ $label }}">
<div class="new-buttons clearfix">
<div class="labels inbox-refresh"><a href="#" class="new-button green-button" label="inbox" style="float: left; margin: 0"><b>Refresh</b><i></i></a></div>
@if($label === 'trash')
<div class="labels"><a href="#" class="new-button empty-trash" style="float: left; margin: 0 0 0 8px"><b>Empty trash</b><i></i></a></div>
@endif
</div>
<div style="clear: both; height: 1px"></div>
<div class="navigation">
@if($label === 'inbox')
<div class="unread-selector"><input type="checkbox" class="unread-only"{{ !empty($unreadOnly) ? ' checked' : '' }}/> Only unread</div>
@endif
@if($rows === [])
<p class="no-messages">{{ $emptyCopy }}</p>
@else
<p>{{ $nav }}</p>
@endif
<div class="progress"></div>
</div>
@foreach($rows as $row)
@php
$status = $row->read_at === null ? 'unread' : 'read';
$stamp = (int) $row->sent_at;
$pretty = date('M j, Y g:i:s A', $stamp);
@endphp
<div class="message-item {{ $status }} " id="msg-{{ (int) $row->id }}">
<div class="message-preview" status="{{ $status }}">
<span class="message-tstamp" isotime="{{ date('Y-m-d\TH:i:s', $stamp) }}" title="{{ $pretty }}">{{ $pretty }}</span>
<img src="{{ \App\Support\Hotel::avatarUrl($row->look ?? '', 's,9,2,sml,1,0') }}" />
<span class="message-sender" title="{{ $row->username }}">{{ $row->username }}</span>
<span class="message-subject" title="{{ $row->subject }}">&ldquo;{{ $row->subject }}&rdquo;</span>
</div>
<div class="message-body" style="display: none;">
<div class="contents"></div>
<div class="message-body-bottom"></div>
</div>
</div>
@endforeach
<div class="navigation">
<div class="progress"></div>
@if($rows !== [])
<p>{{ $nav }}</p>
@endif
</div>
</div>
