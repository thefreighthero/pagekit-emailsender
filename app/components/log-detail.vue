<template>
    <div class="uk-modal-spinner" v-if="!loaded"></div>
    <div v-show="loaded">
        <h2 class="uk-margin-top-remove">{{ 'Details email message' | trans }}</h2>

        <div class="uk-grid">
            <div class="uk-width-medium-3-4">
                <dl class="uk-description-list uk-description-list-horizontal">
                    <dt>{{ 'Recipients' | trans }}</dt>
                    <dd v-for="email in log.recipients">{{ email }}</dd>
                    <dt>{{ 'CC' | trans }}</dt>
                    <template v-if="log.cc.length"><dd v-for="email in log.cc">{{ email }}</dd></template><dd v-else>-</dd>
                    <dt>{{ 'BCC' | trans }}</dt>
                    <template v-if="log.bcc.length"><dd v-for="email in log.bcc">{{ email }}</dd></template><dd v-else>-</dd>
                    <dt>{{ 'Subject' | trans }}</dt>
                    <dd>{{ log.subject }}</dd>
                    <dt>{{ 'Contents' | trans }}</dt>
                    <dd>{{{ log.content }}}</dd>
                </dl>
            </div>
            <div class="uk-width-medium-1-4 uk-form">
7                <dl class="uk-description-list">
                    <dt>{{ 'Sent date' | trans }}</dt>
                    <dd>{{ log.sent | date 'medium' }}</dd>
                    <dt>{{ 'Type' | trans }}</dt>
                    <dd>{{ $root.getTypeLabel(log.type) }}</dd>
                    <dt>{{ 'External key' | trans }}</dt>
                    <dd>{{ log.ext_key || '-' }}</dd>
                    <template v-if="log.data.attachments">
                        <dt>{{ 'Attachments' | trans }}</dt>
                        <dd v-for="attachment in log.data.attachments">{{ attachment }}</dd>
                    </template>
                </dl>
            </div>
        </div>
    </div>

    <div class="uk-modal-footer uk-text-right">
        <button type="button" class="uk-button uk-modal-close">{{ 'Close' | trans }}</button>
    </div>

</template>

<script>

    module.exports = {
        data: function () {
            return {
                log: {recipients: [], cc: [], bcc: [], data: {}},
                loaded: false
            };
        },

        props: ['logid'],

        created: function () {

            this.$root.Logs.query({id: 'detail', log_id: this.logid}).then(function (res) {
                this.$set('log', res.data);
                this.loaded = true;
            }.bind(this));

        },

        beforeDestroy: function () {
            this.$dispatch('close.logmodal');
        }
    };

</script>
