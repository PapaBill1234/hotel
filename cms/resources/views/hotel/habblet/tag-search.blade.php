<div id="tag-search-habblet-container">
<form action="/tag/search" class="search-box">
<input type="text" name="tag" id="search_query" value="{{ $search }}" /><button type="submit">Search</button>
</form>
<p class="search-result-count">{{ $count }} users. 0 groups.</p>
@if($count === 0)
<p>PolarIS has no tags table in this preview. Tag add/remove is not written from this website.</p>
@endif
<table class="search-result"><tbody>
@foreach($rows as $row)
<tr><td><img src="{{ \App\Support\Hotel::avatarUrl((string) $row->look, 's,4,4,sml,1,0') }}" alt="" /></td>
<td><a class="result-title" href="/home/{{ $row->username }}">{{ $row->username }}</a><br />
<span class="result-description">{{ $row->motto }}</span></td></tr>
@endforeach
</tbody></table>
</div>
