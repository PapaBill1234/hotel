@php
$id = (int) $item->id;
$skin = preg_replace('/[^a-z0-9_]/', '', (string) ($item->skin ?: 'defaultskin'));
@endphp
<div class="movable stickie n_skin_{{ $skin }}-c" style=" left: {{ (int) $item->x }}px; top: {{ (int) $item->y }}px; z-index: {{ (int) $item->z }};" id="stickie-{{ $id }}">
	<div class="n_skin_{{ $skin }}" >
		<div class="stickie-header">
			<h3>
@if(!empty($edit))
<img src="/web-gallery/images/myhabbo/icon_edit.gif" width="19" height="18" class="edit-button" id="stickie-{{ $id }}-edit" />
<script language="JavaScript" type="text/javascript">
Event.observe("stickie-{{ $id }}-edit", "click", function(e) { openEditMenu(e, {{ $id }}, "stickie", "stickie-{{ $id }}-edit"); }, false);
</script>
@endif
			</h3>
			<div class="clear"></div>
		</div>
		<div class="stickie-body">
			<div class="stickie-content">
				<div class="stickie-markup">{!! nl2br(e($item->data)) !!}</div>
				<div class="stickie-footer">
				</div>
			</div>
		</div>
	</div>
</div>
