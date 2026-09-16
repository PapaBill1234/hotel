@extends('layouts.hotel-register')
@section('content')
<div id="column1" class="column">
	<div class="habblet-container ">
	@if(!empty($error))
	<div class="action-error flash-message">
		<div class="rounded"><ul><li>{{ $error }}</li></ul></div>
	</div>
	@endif
    <form method="post" action="/register" id="registerform" autocomplete="off">
	@csrf
	<input type="hidden" name="bean.figure" id="register-figure" value="" />
	<input type="hidden" name="bean.gender" id="register-gender" value="" />
	<input type="hidden" name="bean.editorState" id="register-editor-state" value="" />
        <div id="register-column-left" >
            <div id="register-section-2">
                <div class="rounded rounded-blue">
                    <h2 class="heading"><span class="numbering white">2.</span>CHOOSE YOUR NAME</h2>
                    <fieldset id="register-fieldset-name">
	                    <div class="register-label white">{{ $shortname }} name</div>
		                <input type="text" name="bean.avatarName" id="register-name" class="register-text" value="" size="25" />
		                <span id="register-name-check-container" style="display:none">
		                    <a class="new-button search-icon" href="#" id="register-name-check"><b><span></span></b><i></i></a>
		                </span>
                    </fieldset>
                    <div id="name-error-box"></div>
                </div>
            </div>
            <div id="register-section-3">
                <div id="registration-overlay"></div>
	            <div class="cbb clearfix gray">
    	            <h2 class="title heading"><span class="numbering white">3.</span>Your Details	</h2>
    		        <div class="box-content">
                        <fieldset id="register-fieldset-password">
	                        <div class="register-label"><label for="register-password">My password will be:</label></div>
	                        <div class="register-label"><input type="password" name="password" id="register-password" class="register-text" size="25" value="" /></div>
	                        <div class="register-label"><label for="register-password2">Confirm password</label></div>
	                        <div class="register-label"><input type="password" name="retypedPassword" id="register-password2" class="register-text" size="25" value="" /></div>
                        </fieldset>
                        <div id="password-error-box"></div>
                        <fieldset>
	                        <div class="register-label"><label>I was born on:</label></div>
	                        <div id="register-birthday"><select name="bean.day" id="bean_day" class="dateselector"><option value="">Day</option>@for($d=1;$d<=31;$d++)<option value="{{ $d }}">{{ $d }}</option>@endfor</select> <select name="bean.month" id="bean_month" class="dateselector"><option value="">Month</option>@foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $mi => $month)<option value="{{ $mi+1 }}">{{ $month }}</option>@endforeach</select> <select name="bean.year" id="bean_year" class="dateselector"><option value="">Year</option>@for($y=2008;$y>=1900;$y--)<option value="{{ $y }}">{{ $y }}</option>@endfor</select> </div>
                        </fieldset>
                        <div id="email-error-box"></div>
                        <fieldset>
	                        <div class="register-label"><label for="register-email">And my email address is:</label></div>
	                        <div class="register-label"><input type="text" name="bean.email" id="register-email" class="register-text" value="" size="25" maxlength="48" /></div>
	                        <div class="register-label"><label for="register-email2">Retype your email address</label></div>
	                        <div class="register-label"><input type="text" name="bean.retypedEmail" id="register-email2" class="register-text" value="" size="25" maxlength="48" /></div>
                        </fieldset>
	                    <div id="register-marketing-box">
		                    <input type="checkbox" name="bean.marketing" id="bean_marketing" value="true" checked="checked" />
		                    <label for="bean_marketing">Yes, please send me {{ $shortname }} updates, including the newsletter!</label>
	                    </div>
                        <fieldset id="register-fieldset-captcha"><noscript></noscript></fieldset>
                        <div id="terms-error-box"></div>
                        <fieldset id="register-fieldset-terms">
                            <div class="rounded rounded-darkgray" id="register-terms">
	                            <div id="register-terms-content">
	                                <p><a href="/papers/disclaimer" target="_blank" id="register-terms-link">Terms of Service</a></p>
                                    <p class="last">
                                        <input type="checkbox" name="bean.termsOfServiceSelection" id="register-terms-check" value="true" />
                                        <label for="register-terms-check">By clicking on continue, I confirm that I have read and accept the Terms of Use and Privacy Policy.</label>
                                    </p>
                                </div>
                            </div>
                        </fieldset>
		            </div>
	            </div>
	            <div id="form-validation-error-box" style="display:none">
                    <div class="register-error"><div class="rounded rounded-red">Sorry, registration failed. Please check the information you gave in the red boxes.</div></div>
	            </div>
	        </div>
        </div>
        <div id="register-column-right">
            <div id="register-avatar-editor-title">
                <h2 class="heading"><span class="numbering white">1.</span>Create Your {{ $shortname }}</h2>
            </div>
            <div id="avatar-error-box"></div>
            <div id="register-avatar-editor">
                <p><b>You don't have Flash installed. This is why we can only show you a selection of pre-generated {{ $shortname }}s. If you install Flash, you'll be able to choose from the hundreds of different options!</b></p>
                <h3>Girls</h3>
                <div class="register-avatars clearfix">
					@foreach(collect($figures)->where('gender','F') as $figure)
	                <div class="register-avatar" style="background-image: url({{ \App\Support\Hotel::avatarUrl($figure['look'], 'b,4,4,sml,1,0') }})">
	                    <input type="radio" name="randomFigure" value="F-{{ $figure['look'] }}" />
	                </div>
					@endforeach
                </div>
                <h3>Boys</h3>
                <div class="register-avatars clearfix">
					@foreach(collect($figures)->where('gender','M') as $figure)
	                <div class="register-avatar" style="background-image: url({{ \App\Support\Hotel::avatarUrl($figure['look'], 'b,4,4,sml,1,0') }})">
	                    <input type="radio" name="randomFigure" value="M-{{ $figure['look'] }}" />
	                </div>
					@endforeach
	            </div>
                <p>If you dislike the {{ $shortname }} above, you may change it later via the account settings page</p>
            </div>
            <div id="register-buttons">
                <input type="submit" value="Continue" class="continue" id="register-button-continue" />
                <a href="/register/cancel" class="cancel">Exit registration</a>
            </div>
	    </div>
    </form>
	<script type="text/javascript">
	HabboView.add(function() {
		if ($("register-name")) {
			Event.observe($("register-name"), "blur", function() {
				if ($F("register-name") != "" && RegistrationForm.Validator._nameCheckNeeded) {
					RegistrationForm.Validator._checkName();
				}
			});
		}
		var radios = $$("input[name='randomFigure']");
		var applyFigure = function(radio) {
			if (!radio || !radio.value) { return; }
			var value = String(radio.value);
			var dash = value.indexOf("-");
			if (dash < 1) { return; }
			if ($("register-gender")) { $("register-gender").value = value.substring(0, dash); }
			if ($("register-figure")) { $("register-figure").value = value.substring(dash + 1); }
		};
		radios.each(function(radio) {
			Event.observe(radio, "click", function() { applyFigure(radio); });
			Event.observe(radio, "change", function() { applyFigure(radio); });
		});
		if (radios.length > 0 && !$F("register-figure")) {
			radios[0].checked = true;
			applyFigure(radios[0]);
		}
		var origComplete = RegistrationForm.Validator._onCheckNameAvailabilityComplete;
		RegistrationForm.Validator._onCheckNameAvailabilityComplete = function(C, D) {
			if (!D || typeof D !== "object") {
				$("register-name").removeClassName("register-loading");
				RegistrationForm.Validator._ajaxCheckInProgress = false;
				RegistrationForm.Validator._showErrorState($("register-name"), false);
				return;
			}
			origComplete(C, D);
		};
	});
	</script>
				</div>
				<script type="text/javascript">if (!$(document.body).hasClassName('process-template')) { Rounder.init(); }</script>
</div>
@endsection
