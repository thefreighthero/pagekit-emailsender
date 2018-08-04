<?php
$view->style('codemirror');
$view->script('text-edit', 'bixie/emailsender:app/bundle/emailsender-text.js', ['bixie-pkframework', 'editor']); ?>

<div id="text-edit" v-cloak>
	<form class="uk-form" v-validator="form" @submit.prevent="save | valid">

		<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
			<div>

				<h2 class="uk-margin-remove" v-if="text.id">{{ 'Edit text' | trans }}</h2>
				<h2 class="uk-margin-remove" v-else>{{ 'Add text' | trans }}</h2>

			</div>
			<div>

				<a class="uk-button uk-margin-small-right" :href="$url.route('admin/emailsender')">{{ text.id ?
					'Close' :
					'Cancel' | trans }}</a>
				<button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

			</div>
		</div>

        <div class="uk-grid pk-grid-large pk-width-sidebar-large" data-uk-grid-margin>
			<div class="pk-width-content">

                <ul class="uk-tab" v-el:tab>
                    <li><a>{{ 'General' | trans }}</a></li>
                    <li><a>{{ 'Translation' | trans }}</a></li>
                </ul>

                <div class="uk-switcher uk-margin" v-el:content>
                    <div>
                        <div class="uk-form-horizontal uk-margin-bottom">
                            <div class="uk-form-row">
                                <label for="text-to" class="uk-form-label">{{ 'TO addresses' | trans }}</label>
                                <div class="uk-form-controls">
                                    <input id="text-to" name="to" class="uk-form-width-large"
                                           :placeholder="'Separated by &quot;;&quot;' | trans"
                                           v-model="text.data.to"/>
                                </div>
                            </div>

                            <div class="uk-form-row">
                                <label for="text-cc" class="uk-form-label">{{ 'CC addresses' | trans }}</label>
                                <div class="uk-form-controls">
                                    <input id="text-cc" name="cc" class="uk-form-width-large"
                                           :placeholder="'Separated by &quot;;&quot;' | trans"
                                           v-model="text.data.cc"/>
                                </div>
                            </div>

                            <div class="uk-form-row">
                                <label for="text-bcc" class="uk-form-label">{{ 'BCC addresses' | trans }}</label>
                                <div class="uk-form-controls">
                                    <input id="text-bcc" name="bcc" class="uk-form-width-large"
                                           :placeholder="'Separated by &quot;;&quot;' | trans"
                                           v-model="text.data.bcc"/>
                                </div>
                            </div>
                        </div>


                        <div class="uk-form-row uk-form-stacked">
                            <label for="form-title" class="uk-form-subject">{{ 'Subject' | trans }}</label>
                            <div class="uk-form-controls">
                                <input id="form-subject" class="uk-form-large uk-width-1-1" type="text" name="subject"
                                       v-model="text.subject" v-validate:required>
                                <p class="uk-form-help-block uk-text-danger" v-show="form.subject.invalid">{{ 'Subject cannot be blank.' | trans }}</p>
                            </div>
                        </div>

                        <div class="uk-form-row uk-form-stacked">
                            <span class="uk-form-label">{{ 'Content' | trans }}</span>

                            <div class="uk-form-controls">
                                <v-editor id="text-content" :value.sync="text.content"
                                          :options="{markdown: text.data.markdown, mdparser: $marked}"></v-editor>
                            </div>
                        </div>

                        <div class="uk-form-row uk-form-horizontal">
                            <span class="uk-form-label">{{ 'Attachment' | trans }}</span>

                            <div class="uk-form-controls">
                                <input-file :file.sync="text.data.file" root="storage" :ext="['pdf','docx','doc','xls','xlsx']"></input-file>
                            </div>
                        </div>
                    </div>
                    <div>
                        <text-language :text="text"></text-language>
                    </div>
                </div>


			</div>
			<div class="pk-width-sidebar">

				<div class="uk-form-row">
					<label for="text-type" class="uk-form-label">{{ 'Type' | trans }}</label>
					<div class="uk-form-controls">
						<select id="text-type" name="type" class="uk-width-1-1" v-model="text.type" v-validate:required>
							<option value="">{{ 'Select type' | trans }}</option>
							<option v-for="type in types" :value="type.name">{{ type.label }}</option>
						</select>
						<p class="uk-form-help-block uk-text-danger" v-show="form.type.invalid">{{ 'Please select a type!' | trans }}</p>
					</div>
				</div>

                <div class="uk-form-row uk-form-stacked">
                    <label for="form-title" class="uk-form-subject">{{ 'Description' | trans }}</label>
                    <div class="uk-form-controls">
                        <input id="form-description" class="uk-width-1-1" type="text" name="description"
                               v-model="text.description" >
                    </div>
                </div>

                <div class="uk-form-row">
					<label for="text-from_name" class="uk-form-label">{{ 'From name' | trans }} *</label>
					<div class="uk-form-controls">
						<input id="text-from_name" name="from_name" class="uk-width-1-1" v-model="text.data.from_name" v-validate:required/>
						<p class="uk-form-help-block uk-text-danger" v-show="form.from_name.invalid">{{ 'From name is required' | trans }}</p>
					</div>
				</div>

				<div class="uk-form-row">
					<label for="text-from_email" class="uk-form-label">{{ 'From email' | trans }} *</label>
					<div class="uk-form-controls">
						<input id="text-from_email" name="from_email" class="uk-width-1-1" v-model="text.data.from_email" v-validate:required v-validate:email/>
						<p class="uk-form-help-block uk-text-danger" v-show="form.from_email.invalid">{{ 'From email is required' | trans }}</p>
					</div>
				</div>

				<div class="uk-form-row">
					<span class="uk-form-label">{{ 'Use text only for' | trans }}</span>

					<div class="uk-form-controls uk-form-controls-text">
						<p v-for="role in roles" class="uk-form-controls-condensed">
							<label><input type="checkbox" :value="role.id" v-model="text.roles" number> {{ role.name }}</label>
						</p>
					</div>
				</div>

				<h3>{{ 'Available variables' | trans }}</h3>
				<ul class="uk-list">
					<li v-for="key in keys">
						<kbd>$${{ key }}$$</kbd>
					</li>
				</ul>


			</div>

		</div>

	</form>

</div>

