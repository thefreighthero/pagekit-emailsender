<?php $view->script('texts-emailsender', 'bixie/emailsender:app/bundle/emailsender-texts.js', ['vue']) ?>

<div id="emailsender-texts" class="uk-form uk-form-horizontal" v-cloak>

	<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
		<div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

			<h2 class="uk-margin-remove">{{ 'Email Texts' | trans }}</h2>

			<div class="uk-margin-left" v-show="selected.length">
				<ul class="uk-subnav pk-subnav-icon">
					<li><a class="pk-icon-delete pk-icon-hover" :title="'Delete' | trans"
						   data-uk-tooltip="{delay: 500}" @click="removeTexts"
                           v-if="true" v-confirm="'Delete text?' | trans"></a>
					</li>
				</ul>
			</div>

            <div class="pk-search">
                <div class="uk-search">
                    <input class="uk-search-field" type="text" v-model="config.filter.search" debounce="300">
                </div>
            </div>

        </div>
		<div class="uk-position-relative" data-uk-margin>

			<div>
				<a class="uk-button uk-button-primary" :href="$url.route('admin/emailsender/text/edit')">
					{{ 'Add text' | trans }}</a>
			</div>

		</div>
	</div>

	<div class="uk-overflow-container">
		<table class="uk-table uk-table-hover uk-table-middle">
			<thead>
			<tr>
				<th class="pk-table-width-minimum"><input type="checkbox" v-check-all:selected.literal="input[name=id]" number></th>
				<th class="" v-order:subject="config.filter.order">{{ 'Subject' | trans }}</th>
				<th class="pk-table-min-width-200">
					<input-filter :title="$trans('Type')" :value.sync="config.filter.type" :options="typeoptions"></input-filter>
				</th>
				<th class="pk-table-width-100">
					<input-filter :title="$trans('Roles')" :value.sync="config.filter.role" :options="rolesoptions"></input-filter>
				</th>
			</tr>
			</thead>
			<tbody>
			<tr class="check-item" v-for="text in texts" :class="{'uk-active': active(text)}">
				<td><input type="checkbox" name="id" value="{{ text.id }}" number></td>
				<td>
					<a :href="$url.route('admin/emailsender/text/edit', { id: text.id })">{{ text.subject }}</a><br/>
                    <small>{{ text.description }}</small>
				</td>
				<td>
					<em>{{ getTypeLabel(text.type) }}</em>
				</td>
				<td>
					{{ getRoles(text) }}
				</td>
			</tr>
			</tbody>
		</table>
	</div>

	<h3 class="uk-h1 uk-text-muted uk-text-center" v-show="texts && !texts.length">{{ 'No texts found.' | trans
		}}</h3>

	<v-pagination :page.sync="config.page" :pages="pages" v-show="pages > 1"></v-pagination>

</div>
