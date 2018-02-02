/*global _, Vue*/

// @vue/component
const vm = {

    el: '#emailsender-settings',

    name: 'EmailsenderSettings',

    data: () => _.merge({
        config: {},
        form: {},
    }, window.$data),

    methods: {

        save() {
            this.$http.post('admin/emailsender/config', { config: this.config, }).then(() => {
                this.$notify('Settings saved.');
            }, res => this.$notify(res.data, 'danger'));
        },

        addParameter() {
            this.config.url_parameters.push({key: '', value: '',});
        },

    },

};

Vue.ready(vm);
