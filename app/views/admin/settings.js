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
            this.$http.post('admin/system/settings/config', { name: 'bixie/emailsender', config: this.config }).then(function () {
                this.$notify('Settings saved.');
            }, function (res) {
                this.$notify(res.data, 'danger');
            });
        }

    }

};

Vue.ready(module.exports);
