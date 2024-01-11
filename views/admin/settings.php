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
            <span class="uk-form-label">{{ 'Use Brevo' | trans }}</span>
            <div class="uk-form-controls">
                <label for="text-use_brevo" class="uk-form-label">
                    <input id="text-use_brevo" type="checkbox" name="use_brevo"
                           v-model="config.use_brevo"/> {{ 'Use Brevo for transactional mails' | trans }}
                </label>
            </div>
        </div>

        <div class="uk-form-row">
            <span class="uk-form-label">{{ 'Logs' | trans }}</span>
            <div class="uk-form-controls">
                <label for="text-save_logs" class="uk-form-label">
                    <input id="text-save_logs" type="checkbox" name="save_logs"
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

		<hr/>

		<div class="uk-form-row">
			<span class="uk-form-label">{{ 'Images' | trans }}</span>
			<div class="uk-form-controls">
				<label for="text-embed_images" class="uk-form-label">
					<input id="text-embed_images" type="checkbox" name="embed_images"
						   v-model="config.embed_images"/> {{ 'Embed images' | trans }}
				</label>
			</div>
		</div>

		<div v-show="config.embed_images" class="uk-form-row">
			<label for="text-embed_images_maxsize" class="uk-form-label">{{ 'Maximum size for embedding' | trans }}</label>
			<div class="uk-form-controls">
				<input id="text-embed_images_maxsize" type="number" name="embed_images_maxsize" class="uk-form-width-small uk-text-right"
					   v-model="config.embed_images_maxsize" number/>kB
			</div>
		</div>

		<hr/>

		<div class="uk-form-row">
			<span class="uk-form-label">{{ 'URL parameters' | trans }}</span>
			<div class="uk-form-controls uk-form-controls-text">
				<label for="text-add_url_params" class="uk-form-label" style="width: 100%">
					<input id="text-add_url_params" type="checkbox" name="add_url_params"
						   v-model="config.add_url_params"/> {{ 'Add parameters to all links in email' | trans }}
				</label>
                <p class="uk-form-help-block">
                    {{ 'Add the string `$$text.type$$` to add template type to value.' | trans }}
                </p>
			</div>
		</div>

		<div v-show="config.add_url_params" class="uk-form-row">
			<a @click="addParameter" class="uk-form-label">{{ 'Add parameter' | trans }} <i
					class="uk-icon-plus uk-margin-small-left"></i></a>
			<div class="uk-form-controls">

				<div v-for="param in config.url_parameters" class="uk-grid">
					<div class="uk-width-4-10">
						<input type="text" v-model="param.key" :placeholder="'Key' | trans"
							   class="uk-width-1-1"/>
					</div>
					<div class="uk-width-5-10">
						<input type="text" v-model="param.value" :placeholder="'Value' | trans"
							   class="uk-width-1-1"/>
					</div>
					<div class="uk-width-1-10 uk-flex uk-flex-middle uk-flex-center">
						<a class="pk-icon-delete pk-icon-hover" :title="'Delete' | trans"
						   data-uk-tooltip="{delay: 500}" @click="config.url_parameters.$remove(param)"
						   ></a>
					</div>
				</div>

			</div>
		</div>

	</div>

</div>
