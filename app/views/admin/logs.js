module.exports = {

    name: 'logs',

    el: '#emailsender-logs',

    data: function () {
        return _.merge({
            logs: false,
            config: {
                filter: this.$session.get('bixie.emailsender.logs.filter', {order: 'username asc'})
            },
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
            });
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

        getRoles: function (log) {
            var roles_log = this.$trans('All roles');
            if (log.roles.length && log.roles.length !== this.roles.length) {
                roles_log = log.roles.map(function (id) {
                    return _.find(this.roles, 'id', id).name;
                }, this).join(', ');
            }
            return roles_log;
        },

        removeLogs: function () {

            this.Logs.delete({id: 'bulk'}, {ids: this.selected}).then(function () {
                this.load();
                this.$notify('Logs(s) deleted.');
            });
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
                options.push({log: type.label, value: name});
            });

            return [{label: this.$trans('Filter by'), options: options}];
        },

        rolesoptions: function () {

            var options = this.roles.map(function (role) {
                return {log: role.name, value: role.id};
            });

            return [{label: this.$trans('Filter by'), options: options}];
        }

    }


};

Vue.ready(module.exports);