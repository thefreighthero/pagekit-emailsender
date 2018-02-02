/*global _, Vue*/

// @vue/component
const vm = {

    el: '#text-edit',

    data: () => _.merge({
        text: {
            data: {
                markdown: true,
            },
        },
        types: {},
        roles: [],
        form: {},
    }, window.$data),

    ready() {
        this.Texts = this.$resource('api/emailsender/text{/id}');
    },

    computed: {
        keys() {
            return (this.types[this.text.type] ? this.types[this.text.type].keys : [])
        },
    },

    methods: {

        save() {

            let data = {text: this.text,};

            this.$broadcast('save', data);

            this.Texts.save({id: this.text.id,}, data).then(function (res) {
                data = res.data;
                if (!this.text.id) {
                    window.history.replaceState({}, '', this.$url.route('admin/emailsender/text/edit', {id: data.text.id,}));
                }

                this.$set('text', data.text);

                this.$notify(this.$trans('Text %subject% saved.', {subject: this.text.subject,}));

            }, function (data) {
                this.$notify(data, 'danger');
            });
        },

    },

};

Vue.ready(vm);
