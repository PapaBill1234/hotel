@extends('layouts.housekeeping')
@section('content')
<div class="page_title"><span class="page_name">Maintenance mode</span></div>
<div class="page_main"><div class="center">
@if(!empty($notice))<div class="clean-ok">{{ $notice }}</div>@endif
<form method="post">
@csrf
<label><input type="checkbox" name="enabled" value="1" {{ !empty($enabled) ? 'checked' : '' }}> Enable maintenance mode</label>
<button>Save</button>
</form>
</div></div>
@endsection
