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
			<label class="uk-form-label">{{ 'Countries suppliers' | trans }}</label>
			<div class="uk-form-controls">
				<select v-model="config.countries_suppliers" size="8" multiple="multiple"
						class="uk-form-width-medium">
					<option v-for="country in countries" :value="$key">{{ country }}</option>
				</select>
			</div>
		</div>

	</div>

</div>
