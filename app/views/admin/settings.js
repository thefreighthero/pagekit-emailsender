module.exports = {

    el: '#emailsender-settings',

    data: function () {
        return _.merge({
            config: {},
            form: {}
        }, window.$data);
    },

    methods: {

        save: function () {
            this.$http.post('admin/emailsender/config', { config: this.config }).then(function () {
                this.$notify('Settings saved.');
            }, function (res) {
                this.$notify(res.data, 'danger');
            });
        },

        addParameter: function () {
            this.config.url_parameters.push({key: '', value: ''});
        }

    }

};

Vue.ready(module.exports);
