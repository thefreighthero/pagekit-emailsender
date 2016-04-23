module.exports = {

    name: 'texts',

    el: '#emailsender-texts',

    data: function () {
        return _.merge({
            texts: false,
            config: {
                filter: this.$session.get('bixie.emailsender.texts.filter', {order: 'username asc'})
            },
            pages: 0,
            count: '',
            roles: [],
            types: {},
            selected: []
        }, window.$data);
    },

    created: function () {
        this.Texts = this.$resource('api/emailsender/text{/id}');
        this.$watch('config.page', this.load, {immediate: true});
    },

    methods: {

        load: function () {
            return this.Texts.query(this.config).then(function (res) {
                this.$set('texts', res.data.texts);
            });
        },

        active: function (text) {
            return this.selected.indexOf(text.id) !== -1;
        },

        getSelected: function () {
            return this.texts.filter(function (text) { return this.selected.indexOf(text.id) !== -1; }, this);
        },

        getTypeLabel: function (name) {
            return this.types[name] ? this.types[name].label : name;
        },

        getRoles: function (text) {
            var roles_text = this.$trans('All roles');
            if (text.roles.length && text.roles.length !== this.roles.length) {
                roles_text = text.roles.map(function (id) {
                    return _.find(this.roles, 'id', id).name;
                }, this).join(', ');
            }
            return roles_text;
        },

        removeTexts: function () {

            this.Texts.delete({id: 'bulk'}, {ids: this.selected}).then(function () {
                this.load();
                this.$notify('Texts(s) deleted.');
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

                this.$session.set('bixie.emailsender.texts.filter', filter);
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
        },

        rolesoptions: function () {

            var options = this.roles.map(function (role) {
                return {text: role.name, value: role.id};
            });

            return [{label: this.$trans('Filter by'), options: options}];
        }

    }


};

Vue.ready(module.exports);

