<table border="0" cellpadding="0" cellspacing="0" width="100%" class="group-postlist-list" id="group-postlist-list">
<tr class="post-list-index-preview">
	<td class="post-list-row-container">
		<a href="/home/{{ $author->username }}" class="post-list-creator-link post-list-creator-info">{{ $author->username }}</a>
		<img alt="{{ $online }}" src="/web-gallery/images/myhabbo/habbo_{{ $online }}.gif" />
		<div class="post-list-posts post-list-creator-info">Message: 0</div>
		<div class="clearfix">
			<div class="post-list-creator-avatar"><img src="{{ \App\Support\Hotel::avatarUrl($author->look ?? '', 'b,2,2,,1,0') }}" alt="" /></div>
			<div class="post-list-group-badge"></div>
			<div class="post-list-avatar-badge"></div>
		</div>
		<div class="post-list-motto post-list-creator-info">{{ $author->motto ?? '' }}</div>
	</td>
	<td class="post-list-message" valign="top" colspan="2">
		<a href="#" id="edit-post-message" class="resume-edit-link">&laquo; Edit</a>
		<span class="post-list-message-header"> {{ $name }}</span><br />
		<span class="post-list-message-time">{{ date('M j, Y (g:i A)') }}</span>
		<div class="post-list-report-element"></div>
		<div class="post-list-content-element">
			{!! $message !!}
		</div>
		<div>
			<div class="button-area">
				<a id="topic-form-cancel-preview" class="new-button red-button cancel-icon" href="#"><b><span></span>Cancel</b><i></i></a>
				<a id="topic-form-save-preview" class="new-button green-button save-icon" href="#"><b><span></span>Save</b><i></i></a>
			</div>
		</div>
	</td>
</tr>
</table>
