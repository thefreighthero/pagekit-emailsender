/*global _, Vue*/

// @vue/component
const vm = {

    el: '#emailsender-texts',

    name: 'Texts',

    data() {
        return _.merge({
            texts: false,
            config: {
                filter: this.$session.get('bixie.emailsender.texts.filter', {search: '', order: 'username asc',}),
            },
            pages: 0,
            count: '',
            roles: [],
            types: {},
            selected: [],
        }, window.$data);
    },

    computed: {

        typeoptions() {

            let options = [];
            _.forIn(this.types, function (type, name) {
                options.push({text: type.label, value: name,});
            });

            return [{label: this.$trans('Filter by'), options: options,},];
        },

        rolesoptions() {

            let options = this.roles.map(function (role) {
                return {text: role.name, value: role.id,};
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

                this.$session.set('bixie.emailsender.texts.filter', filter);
            },
            deep: true,
        },

    },

    created() {
        this.Texts = this.$resource('api/emailsender/text{/id}');
        this.$watch('config.page', this.load, {immediate: true,});
    },

    methods: {

        load() {
            return this.Texts.query(this.config).then(res => {
                this.$set('texts', res.data.texts);
                this.$set('pages', res.data.pages);
                this.$set('count', res.data.count);
                this.$set('selected', []);
            });
        },

        active(text) {
            return this.selected.indexOf(text.id) !== -1;
        },

        getSelected() {
            return this.texts.filter(function (text) { return this.selected.indexOf(text.id) !== -1; }, this);
        },

        getTypeLabel(name) {
            return this.types[name] ? this.types[name].label : name;
        },

        getRoles(text) {
            let roles_text = this.$trans('All roles');
            if (text.roles.length && text.roles.length !== this.roles.length) {
                roles_text = text.roles.map(function (id) {
                    return _.find(this.roles, 'id', id).name;
                }, this).join(', ');
            }
            return roles_text;
        },

        removeTexts() {

            this.Texts.delete({id: 'bulk',}, {ids: this.selected,}).then(() => {
                this.load();
                this.$notify('Texts(s) deleted.');
            });
        },

    },

};

Vue.ready(vm);

