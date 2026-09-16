<form action="#" method="post" id="group-settings-form">
	<div id="group-settings">
		<p>Room moves are unavailable here. Use the game client for room changes.</p>
		<p>PolarIS guilds are not written from this website. Name, type, and forum settings stay in the hotel.</p>
		<div id="group-settings-data" class="group-settings-pane">
			<div id="group-logo">
				<img src="/web-gallery/images/groups/group_icon.gif" />
			</div>
			<div id="group-identity-area">
				<div id="group-name-area">
					<div id="group_name_message_error" class="error"></div>
					<label for="group_name" id="group_name_text">Edit group name:</label>
					<input type="text" name="group_name" id="group_name" value="{{ $guild->name }}"/><br />
				</div>
				<div id="group-url-area">
					<div id="group_url_message_error" class="error"></div>
					<label for="group_url" id="group_url_text">Edit group URL:</label><br/>
					@if($alias === '')
					<input type="text" name="group_url" id="group_url" value=""/><br />
					<input type="hidden" name="group_url_edited" id="group_url_edited" value="1"/>
					@else
					<span id="group_url_text"><a href="/groups/{{ $alias }}">/groups/{{ $alias }}</a></span><br/>
					<input type="hidden" name="group_url" id="group_url" value="{{ $alias }}"/>
					<input type="hidden" name="group_url_edited" id="group_url_edited" value="0"/>
					@endif
				</div>
			</div>
			<div id="group-description-area">
				<div id="group_description_message_error" class="error"></div>
				<label for="group_description" id="description_text">Edit text:</label>
				<textarea name="group_description" id="group_description">{{ $guild->description }}</textarea>
			</div>
		</div>
		<div id="group-settings-type" class="group-settings-pane group-settings-selection">
			<label for="group_type">Edit group type:</label>
			<input type="radio" name="group_type" id="group_type" value="0"{{ (int) $guild->state === 0 ? ' checked="checked"' : '' }} />
			<div class="description">
				<div class="group-type-normal">Regular</div>
				<p>Anyone can join. 50,000 member limit.</p>
			</div>
			<input type="radio" name="group_type" id="group_type" value="1"{{ (int) $guild->state === 1 ? ' checked="checked"' : '' }} />
			<div class="description">
				<div class="group-type-exclusive">Exclusive</div>
				<p>Group members must be accepted.</p>
			</div>
			<input type="radio" name="group_type" id="group_type" value="2"{{ (int) $guild->state === 2 ? ' checked="checked"' : '' }} />
			<div class="description">
				<div class="group-type-private">Private</div>
				<p>Closed group.</p>
			</div>
			<input type="hidden" id="initial_group_type" value="{{ (int) $guild->state }}">
		</div>
	</div>
	<div id="group-button-area">
		<a href="#" id="group-settings-update" class="new-button"><b>Save</b><i></i></a>
		<a href="#" id="group-delete" class="new-button red-button cancel-icon"><b><span></span>Delete</b><i></i></a>
	</div>
</form>
