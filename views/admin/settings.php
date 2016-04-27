<?php $view->script('emailsender-settings', 'bixie/emailsender:app/bundle/emailsender-settings.js', ['vue']) ?>

<div id="emailsender-settings" class="uk-form">


	<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
		<div data-uk-margin>

			<h2 class="uk-margin-remove">{{ 'Email Sender Settings' | trans }}</h2>

		</div>
		<div data-uk-margin>

			<button class="uk-button uk-button-primary" @click="save">{{ 'Save' | trans }}</button>

		</div>
	</div>

	<div class="uk-form-horizontal">

		<div class="uk-form-row">
			<span class="uk-form-label">{{ 'Logs' | trans }}</span>
			<div class="uk-form-controls">
				<label for="text-save_logs" class="uk-form-label">
					<input id="text-save_logs" type="checkbox" name="save_logs" class="uk-form-width-large"
						   v-model="config.save_logs"/> {{ 'Save logs' | trans }}
				</label>
			</div>
		</div>

		<div class="uk-form-row">
			<label for="text-from_name" class="uk-form-label">{{ 'From name' | trans }}</label>
			<div class="uk-form-controls">
				<input id="text-from_name" type="text" name="from_name" class="uk-form-width-large" v-model="config.from_name"/>
			</div>
		</div>

		<div class="uk-form-row">
			<label for="text-from_email" class="uk-form-label">{{ 'From email' | trans }}</label>
			<div class="uk-form-controls">
				<input id="text-from_email" type="text" name="from_email" class="uk-form-width-large" v-model="config.from_email"/>
			</div>
		</div>

	</div>

</div>
