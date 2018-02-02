/*global _, Vue*/
import LogDetail from '../../components/log-detail.vue';

// @vue/component
const vm = {

    el: '#emailsender-logs',

    name: 'Logs',

    components: {
        'logdetail': LogDetail,
    },

    data() {
        return _.merge({
            logs: false,
            config: {
                filter: this.$session.get('bixie.emailsender.logs.filter', {
                    search: '', order: 'sent desc', type: '',
                }),
            },
            logID: 0,
            pages: 0,
            count: '',
            roles: [],
            types: {},
            selected: [],
        }, window.$data);
    },

    computed: {

        typeoptions() {

            let options = [{label: this.$trans('External'), value: 'External',},];
            _.forIn(this.types, function (type, name) {
                options.push({text: type.label, value: name,});
            });

            return [{label: this.$trans('Filter by'), options: options,},];
        },
    },

    watch: {

        'config.filter': {
            handler(filter) {
                if (this.config.page) {
                    this.config.page = 0;
                } else {
                    this.load();
                }

                this.$session.set('bixie.emailsender.logs.filter', filter);
            },
            deep: true,
        },

    },

    created() {
        this.Logs = this.$resource('api/emailsender/log{/id}');
        this.$watch('config.page', this.load, {immediate: true,});
    },

    methods: {

        load() {
            return this.Logs.query(this.config).then(res => {
                this.$set('logs', res.data.logs);
                this.$set('pages', res.data.pages);
                this.$set('count', res.data.count);
                this.$set('selected', []);
                this.checkDetailHash();
            });
        },

        checkDetailHash() {
            if (this.$url.current.hash) {
                let id = parseInt(this.$url.current.hash, 10), log = _.find(this.logs, log => log.id === id);
                if (log) {
                    this.logDetails(log);
                }
            }
        },

        active(log) {
            return this.selected.indexOf(log.id) !== -1;
        },

        getSelected() {
            return this.logs.filter(log => this.selected.indexOf(log.id) !== -1);
        },

        getTypeLabel(name) {
            return this.types[name] ? this.types[name].label : name;
        },

        removeLogs() {
            this.Logs.delete({id: 'bulk',}, {ids: this.selected,}).then(() => {
                this.load();
                this.$notify('Logs(s) deleted.');
            });
        },

        logDetails(log) {
            window.history.replaceState({}, '', this.$url.current.href.replace('#' + this.$url.current.hash, '') + '#' + log.id);
            this.$url.current.hash = '#' + log.id;
            this.logID = log.id;
            this.$refs.logmodal.open();
        },

    },

};

Vue.ready(vm);