{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/" version="2.0">
  <channel>
    <title>{{ $shortname }} ~</title>
    <link>{{ url('/') }}</link>
    <description />
@foreach($items as $row)
    <item>
      <title>{{ $row->title }}</title>
      <link>{{ url('/articles/'.$row->id.'-'.\App\Support\Hotel::newsSlug($row->title)) }}</link>
      <description>{{ $row->summary }}</description>
      <pubDate>{{ date('D, j M Y H:i:s e', (int) $row->time) }}</pubDate>
      <guid isPermaLink="false">{{ url('/articles/'.$row->id.'-'.\App\Support\Hotel::newsSlug($row->title)) }}</guid>
      <dc:date>{{ date('c', (int) $row->time) }}</dc:date>
    </item>
@endforeach
  </channel>
</rss>
