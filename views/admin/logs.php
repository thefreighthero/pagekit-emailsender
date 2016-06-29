<?php $view->script('emailsender-logs', 'bixie/emailsender:app/bundle/emailsender-logs.js', ['vue']) ?>

<div id="emailsender-logs" class="uk-form uk-form-horizontal" v-cloak>

	<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
		<div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

			<h2 class="uk-margin-remove">{{ 'Email logs' | trans }}</h2>

			<div class="uk-margin-left" v-show="selected.length">
				<ul class="uk-subnav pk-subnav-icon">
					<li><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}"
						   data-uk-tooltip="{delay: 500}" @click.prevent="removeLogs"
						   v-confirm="'Delete logs? All data will be deleted from the database.' | trans"></a>
					</li>
				</ul>
			</div>

		</div>
		<div class="uk-position-relative" data-uk-margin>

			<div data-uk-dropdown="{ mode: 'click' }">
				<button class="uk-button" @click="$refs.csvmodal.open()">
					{{ 'Export csv' | trans }}</button>

			</div>

		</div>
	</div>

	<div class="uk-overflow-container">
		<table class="uk-table uk-table-hover uk-table-middle">
			<thead>
			<tr>
				<th class="pk-table-width-minimum"><input type="checkbox" v-check-all:selected.literal="input[name=id]" number></th>
				<th class="pk-table-width-200" v-order:sent="config.filter.order">{{ 'Sent date' | trans }}</th>
				<th class="pk-table-min-width-100" v-order:recipients="config.filter.order">{{ 'Recipients' | trans }}</th>
				<th class="pk-table-min-width-200" v-order:subject="config.filter.order">{{ 'Subject' | trans }}</th>
				<th class="pk-table-width-200">
					<input-filter :title="$trans('Type')" :value.sync="config.filter.type" :options="typeoptions"></input-filter>
				</th>
			</tr>
			</thead>
			<tbody>
			<tr class="check-item" v-for="log in logs" :class="{'uk-active': active(log)}">
				<td><input type="checkbox" name="id" value="{{ log.id }}"></td>
				<td>
					<a @click.prevent="logDetails(log)">{{ log.sent | date 'medium' }}</a>
				</td>
				<td class="pk-table-text-break">
					{{ log.recipients.join(', ') }}
				</td>
				<td class="pk-table-text-break">
					{{ log.subject }}
				</td>
				<td>
					{{ getTypeLabel(log.type) }}
				</td>
			</tr>
			</tbody>
		</table>
	</div>

	<h3 class="uk-h1 uk-text-muted uk-text-center" v-show="logs && !logs.length">{{ 'No logs found.' | trans
		}}</h3>

	<v-pagination :page.sync="config.page" :pages="pages" v-show="pages > 1"></v-pagination>

	<v-modal v-ref:logmodal large>
		<logdetail :logid="logID"></logdetail>
	</v-modal>


</div>
