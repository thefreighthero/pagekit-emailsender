module.exports = {

    name: 'logs',

    el: '#emailsender-logs',

    data: function () {
        return _.merge({
            logs: false,
            config: {
                filter: this.$session.get('bixie.emailsender.logs.filter', {search: '', order: 'sent desc', type: ''})
            },
            logID: 0,
            pages: 0,
            count: '',
            roles: [],
            types: {},
            selected: []
        }, window.$data);
    },

    created: function () {
        this.Logs = this.$resource('api/emailsender/log{/id}');
        this.$watch('config.page', this.load, {immediate: true});
    },

    methods: {

        load: function () {
            return this.Logs.query(this.config).then(function (res) {
                this.$set('logs', res.data.logs);
                this.$set('pages', res.data.pages);
                this.$set('count', res.data.count);
                this.$set('selected', []);
                this.checkDetailHash();
            });
        },

        checkDetailHash: function () {
            if (this.$url.current.hash) {
                var id = parseInt(this.$url.current.hash, 10), log = _.find(this.logs, function (log) {
                    return log.id === id;
                });
                if (log) {
                    this.logDetails(log);
                }
            }
        },

        active: function (log) {
            return this.selected.indexOf(log.id) !== -1;
        },

        getSelected: function () {
            return this.logs.filter(function (log) { return this.selected.indexOf(log.id) !== -1; }, this);
        },

        getTypeLabel: function (name) {
            return this.types[name] ? this.types[name].label : name;
        },

        removeLogs: function () {

            this.Logs.delete({id: 'bulk'}, {ids: this.selected}).then(function () {
                this.load();
                this.$notify('Logs(s) deleted.');
            });
        },

        logDetails: function (log) {
            window.history.replaceState({}, '', this.$url.current.href.replace('#' + this.$url.current.hash, '') + '#' + log.id);
            this.$url.current.hash = '#' + log.id;
            this.logID = log.id;
            this.$refs.logmodal.open();
        }

    },

    watch: {

        'config.filter': {
            handler: function (filter) {
                if (this.config.page) {
                    this.config.page = 0;
                } else {
                    this.load();
                }

                this.$session.set('bixie.emailsender.logs.filter', filter);
            },
            deep: true
        }

    },

    computed: {

        typeoptions: function () {

            var options = [];
            _.forIn(this.types, function (type, name) {
                options.push({text: type.label, value: name});
            });

            return [{label: this.$trans('Filter by'), options: options}];
        }
    },

    components: {
        'logdetail': require('../../components/log-detail.vue')
    }


};

Vue.ready(module.exports);