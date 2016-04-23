module.exports = {

    el: '#text-edit',

    data: function () {
        return _.merge({
            text: {
                data: {
                    markdown: true
                }
            },
            types: {},
            roles: [],
            form: {}
        }, window.$data);
    },

    ready: function () {
        this.Texts = this.$resource('api/emailsender/text{/id}');
    },

    computed: {
        keys: function () {
            return (this.types[this.text.type] ? this.types[this.text.type].keys : [])
        }
    },

    methods: {

        save: function () {

            var data = {text: this.text};

            this.$broadcast('save', data);

            this.Texts.save({id: this.text.id}, data).then(function (res) {
                data = res.data;
                if (!this.text.id) {
                    window.history.replaceState({}, '', this.$url.route('admin/emailsender/text/edit', {id: data.text.id}));
                }

                this.$set('text', data.text);

                this.$notify(this.$trans('Text %subject% saved.', {subject: this.text.subject}));

            }, function (data) {
                this.$notify(data, 'danger');
            });
        }

    }

};

Vue.ready(module.exports);
