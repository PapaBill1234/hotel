@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">Updates</span></div>
<div class="page_main"><div class="center">
<div class="clean-gray">Automatic update checks are disabled. The previous implementation fetched a plain-HTTP response and passed it to unserialize(), which is unsafe. No verified HTTPS JSON endpoint is configured.</div>
</div></div>
@endsection
