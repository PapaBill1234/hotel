@if($friends->isEmpty())
<p class="last" style="padding-top: 11px">You don't have any friends</p>
@else
<div id="friend-list" class="clearfix">
<div id="friend-list-header-container" class="clearfix">
	<div id="friend-list-header">
		<div class="page-limit">
			<div class="big-icons friend-header-icon">Friends
				<br />Show
				@if($pageSize === 30)
				30 | <a class="category-limit" id="pagelimit-50">50</a> | <a class="category-limit" id="pagelimit-100">100</a>
				@elseif($pageSize === 50)
				<a class="category-limit" id="pagelimit-30">30</a> | 50 | <a class="category-limit" id="pagelimit-100">100</a>
				@else
				<a class="category-limit" id="pagelimit-30">30</a> | <a class="category-limit" id="pagelimit-50">50</a> | 100
				@endif
			</div>
		</div>
	</div>
	<div id="friend-list-paging"></div>
</div>
<form id="friend-list-form">
	<table id="friend-list-table" border="0" cellpadding="0" cellspacing="0">
		<thead>
			<tr class="friend-list-header">
				<th class="friend-select" />
				<th class="friend-name"><a class="sort">Name</a></th>
				<th class="friend-login"><a class="sort">Last logon</a></th>
				<th class="friend-remove">Remove</th>
			</tr>
		</thead>
		<tbody>
		@foreach($friends as $i => $row)
			<tr class="{{ $i % 2 ? 'even' : 'odd' }}">
				<td><input type="checkbox" name="friendList[]" value="{{ (int) $row->id }}" /></td>
				<td class="friend-name">{{ $row->username }}</td>
				<td class="friend-login" title="{{ date('n/j/y g:i A', (int) $row->last_online) }}">{{ date('n/j/y g:i A', (int) $row->last_online) }}</td>
				<td class="friend-remove"><div id="remove-friend-button-{{ (int) $row->id }}" class="friendmanagement-small-icons friendmanagement-remove remove-friend"></div></td>
			</tr>
		@endforeach
		</tbody>
	</table>
	<a class="select-all" id="friends-select-all" href="#">Select all</a> |
	<a class="deselect-all" href="#" id="friends-deselect-all">Deselect all</a>
</form>
<div id="category-options" class="clearfix">
	<div class="friend-del"><a class="new-button red-button cancel-icon" href="#" id="delete-friends"><b><span></span>Delete selected friends</b><i></i></a></div>
</div>
</div>
@endif
