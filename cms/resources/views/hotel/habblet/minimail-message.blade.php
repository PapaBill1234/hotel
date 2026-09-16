<div class="message-body-content">
<p><b>{{ $row->subject }}</b></p>
<p>From: {{ $row->username }}</p>
<p>{!! nl2br(e($row->body)) !!}</p>
</div>
