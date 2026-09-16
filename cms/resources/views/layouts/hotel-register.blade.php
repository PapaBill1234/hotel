<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>{{ $shortname }}: Register </title>
<script type="text/javascript">var andSoItBegins = (new Date()).getTime();</script>
<link rel="shortcut icon" href="/web-gallery/v2/favicon.ico" type="image/vnd.microsoft.icon" />
<script src="/web-gallery/static/js/libs2.js" type="text/javascript"></script>
<script src="/web-gallery/static/js/visual.js" type="text/javascript"></script>
<script src="/web-gallery/static/js/libs.js" type="text/javascript"></script>
<script src="/web-gallery/static/js/common.js" type="text/javascript"></script>
<script src="/web-gallery/static/js/fullcontent.js" type="text/javascript"></script>
<link rel="stylesheet" href="/web-gallery/v2/styles/style.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/v2/styles/buttons.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/v2/styles/boxes.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/v2/styles/tooltips.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/v2/styles/process.css" type="text/css" />
<link rel="stylesheet" href="/web-gallery/v2/styles/registration.css" type="text/css" />
<script src="/web-gallery/static/js/registration.js" type="text/javascript"></script>
<script type="text/javascript">
document.habboLoggedIn = false;
var habboName = null;
var ad_keywords = "";
var habboReqPath = "";
var habboStaticFilePath = "/web-gallery";
var habboImagerUrl = "/habbo-imaging/";
var habboPartner = "";
var habboDefaultClientPopupUrl = "/client";
window.name = "habboMain";
if (typeof HabboClient != "undefined") { HabboClient.windowName = "client"; }
L10N.put("register.tooltip.name", "Your name can contain lowercase and uppercase letters, numbers and the characters -=?!@:.");
L10N.put("register.tooltip.password", "Your password must have at least 6 characters and it must contain both letters and numbers.");
L10N.put("register.error.password_required", "Please enter a password");
L10N.put("register.error.password_too_short", "Your password should be at least six characters long");
L10N.put("register.error.password_numbers", "You need to have at least one number or special character in your password.");
L10N.put("register.error.password_letters", "You need to have at least one lowercase or UPPERCASE letter in your password.");
L10N.put("register.error.retyped_password_required", "Please re-enter your password");
L10N.put("register.error.retyped_password_notsame", "Your passwords do not match, please try again");
L10N.put("register.error.retyped_email_required", "Please type your email again");
L10N.put("register.error.retyped_email_notsame", "Emails don't match");
L10N.put("register.tooltip.namecheck", "Click here to check your name is free.");
L10N.put("register.tooltip.retypepassword", "Please re-enter your password.");
L10N.put("register.tooltip.personalinfo.disabled", "Please choose your {{ $shortname }} (character) name first.");
L10N.put("register.tooltip.namechecksuccess", "Congratulations! The name is available.");
L10N.put("register.tooltip.passwordsuccess", "Your password is now secure.");
L10N.put("register.tooltip.passwordtooshort", "The password you have chosen is too short.");
L10N.put("register.tooltip.passwordnotsame", "Password not the same, please re-type it.");
L10N.put("register.tooltip.invalidpassword", "The password you have chosen is invalid, please choose a new password.");
L10N.put("register.tooltip.email", "Please enter your email address. You need to activate your account using this address so please use your real address.");
L10N.put("register.tooltip.retypeemail", "Please re-enter your email address.");
L10N.put("register.tooltip.invalidemail", "Please enter a valid email address.");
L10N.put("register.tooltip.emailsuccess", "You have provided a valid email address, thanks!");
L10N.put("register.tooltip.emailnotsame", "Your retyped email doesn't match.");
L10N.put("register.tooltip.enterpassword", "Please enter a password.");
L10N.put("register.tooltip.entername", "Please enter a name for your {{ $shortname }} (character).");
L10N.put("register.tooltip.enteremail", "Please enter your email address.");
L10N.put("register.tooltip.enterbirthday", "Please give your date of birth - you need this later to get password reminders etc.");
L10N.put("register.tooltip.acceptterms", "Please accept the Terms and Conditions");
L10N.put("register.tooltip.invalidbirthday", "Please supply a valid birthdate");
L10N.put("register.tooltip.emailandparentemailsame","You parent's email and your email cannot be the same, please provide a different one.");
L10N.put("register.tooltip.entercaptcha","Enter the code.");
L10N.put("register.tooltip.captchavalid","Invalid code.");
L10N.put("register.tooltip.captchainvalid","Invalid code, please try again.");
L10N.put("register.error.parent_permission","You need to tell your parents about this service");
RegistrationForm.parentEmailAgeLimit = -1;
RegistrationForm.isCaptchaEnabled = false;
RegistrationForm.ageLimit = -1;
RegistrationForm.banHours = 24;
HabboView.add(function() {
    if ($("register-avatar-editor-title")) { Rounder.addCorners($("register-avatar-editor-title"), 4, 4, "rounded-container"); }
    RegistrationForm.init(true);
});
</script>
<meta name="build" content="PHPRetro 4.0.10 BETA" />
</head>
<body id="landing" class="process-template">
<div id="overlay"></div>
<div id="container">
	<div class="cbb process-template-box clearfix">
		<div id="content">
			<div id="header" class="clearfix">
				<h1><a href="/"></a></h1>
				<ul class="stats">
					<li class="stats-online"><span class="stats-fig">{{ $onlineCount }}</span> {{ $shortname }}s online now</li>
					<li class="stats-visited"><img src="/web-gallery/v2/images/{{ $hotelOnline ? 'online' : 'offline' }}.gif" alt="{{ $hotelOnline ? 'online' : 'offline' }}" border="0"></li>
				</ul>
			</div>
			<div id="process-content">
				@yield('content')
				@include('hotel.partials.footer-process')
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">if (typeof HabboView != "undefined") { HabboView.run(); }</script>
</body>
</html>
