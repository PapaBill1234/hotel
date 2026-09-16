@extends('layouts.housekeeping')
@section('content')
<div class="page_title">
 <img src="/housekeeping/images/icons/newsletter.png" class="pticon">
 <span class="page_name_shadow">Newsletter</span>
 <span class="page_name">Newsletter</span>
</div>
<div class="page_main">
<table border="0" cellpadding="0" cellspacing="0" height="100%">
<tbody>
<tr height="100%">
<td class="page_main_left">
<div class="left_date">{{ $hkDate }}</div>
<div class="hr"></div>
<div class="text">Here, you can send newsletters to everyone who opted to do so via Account Settings. You can edit the default template or create your own message. HTML is allowed.</div>
</td>
<td class="page_main_right">
<div class="center">
@if(!empty($error))<div class="clean-error">{{ $error }}</div>@endif
@if(!empty($notice))<div class="clean-ok">{{ $notice }}</div>@endif
<div class="settings">
<form name="settings" action="/housekeeping/newsletter" method="POST">@csrf
<label for="subject">Subject:</label><br />
<input type="text" name="subject" value="" title="Subject of the email you're sending." /><br />
<label for="message">Message:</label><br />
<textarea name="message" title="The email to be sent.">{{ $defaultMessage }}</textarea><br />
<label for="header">Header:</label><br />
<input type="text" name="header" title="The HTML header file to include in the email." value="./templates/newsletter_header.php" />
<label for="footer">Footer:</label><br />
<input type="text" name="footer" title="The HTML footer file to include in the email." value="./templates/newsletter_footer.php" />
<label for="status">Status:</label><br />
<input type="checkbox" style="width: auto" name="status" value="true" checked="true" title="If you check this, you can see exactly how many emails are sent and any errors that occur." /><lable class="checklabel" for="status">Show</label><br />
<div class="button"><input type="submit" name="send" value="Send" /></div>
</form>
</div>
</div>
</td>
</tr>
</tbody>
</table>
</div>
@endsection
