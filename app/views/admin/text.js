/*global _, Vue, UIkit*/
import TextLanguage from '../../components/text-language.vue';

// @vue/component
const vm = {

    el: '#text-edit',

    name: 'Text',

    components: {
        'text-language': TextLanguage,
    },

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

    computed: {
        keys() {
            return (this.types[this.text.type] ? this.types[this.text.type].keys : [])
        },
    },

    ready() {
        this.Texts = this.$resource('api/emailsender/text{/id}');
        this.tab = UIkit.tab(this.$els.tab, {connect: this.$els.content,});
    },


    methods: {
        save() {

            let data = {text: this.text,};

            this.$broadcast('save', data);

            this.Texts.save({id: this.text.id,}, data).then(res => {
                data = res.data;
                if (!this.text.id) {
                    window.history.replaceState({}, '', this.$url.route('admin/emailsender/text/edit', {id: data.text.id,}));
                }

                this.$set('text', data.text);

                this.$notify(this.$trans('Text %subject% saved.', {subject: this.text.subject,}));

            }, res => {
                this.$notify((res.data.message || res.data), 'danger');
            });
        },

    },

};

Vue.ready(vm);
