@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">Site settings</span></div>
<div class="page_main"><div class="center">
@if(!empty($notice))<div class="clean-ok">{{ $notice }}</div>@endif
<p>Only keys already in <code>phpretro_site_settings</code> can be changed. New keys are not created from this form.</p>
<form method="post">@csrf
@foreach($settingsRows as $key => $value)
<label for="setting-{{ $key }}">{{ $key }}</label><br>
@if(strlen((string) $value) > 80)
<textarea id="setting-{{ $key }}" name="{{ $key }}" rows="6" cols="60">{{ $value }}</textarea><br>
@else
<input id="setting-{{ $key }}" name="{{ $key }}" value="{{ $value }}"><br>
@endif
@endforeach
<button>Save</button>
</form>
</div></div>
@endsection
