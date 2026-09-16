@php $id = (int) $item->id; $css = $homes->itemCss('sticker', $item->catalogue_data); @endphp
    <div class="movable sticker {{ $css }}" style="left: {{ (int) $item->x }}px; top: {{ (int) $item->y }}px; z-index: {{ (int) $item->z }}" id="sticker-{{ $id }}">
@if(!empty($edit))
<img src="/web-gallery/images/myhabbo/icon_edit.gif" width="19" height="18" class="edit-button" id="sticker-{{ $id }}-edit" />
<script language="JavaScript" type="text/javascript">
Event.observe("sticker-{{ $id }}-edit", "click", function(e) { openEditMenu(e, {{ $id }}, "sticker", "sticker-{{ $id }}-edit"); }, false);
</script>
@endif
    </div>
