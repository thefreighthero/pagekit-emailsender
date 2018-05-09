<template>

    <div class="uk-grid">

        <div class="uk-width-medium-1-4">

            <div class="uk-panel">

                <ul class="uk-nav uk-nav-side pk-nav-large" data-uk-tab="connect: '#tab-widget-languages'">
                    <li v-for="locale in language_tabs">
                        <a><img :src="getFlagSource(locale.language)" width="40px" class="uk-margin-small-right" alt=""/>{{ locale.language }}</a>
                    </li>
                </ul>

            </div>

        </div>
        <div class="uk-width-medium-3-4">

            <ul id="tab-widget-languages" class="uk-switcher uk-margin uk-form-stacked">
                <li v-for="locale in language_tabs">
                    <div class="uk-form-row uk-form-horizontal">
                        <label class="uk-form-label">{{ 'Title' | trans }}</label>

                        <div class="uk-form-controls">
                            <input class="uk-width-1-1 uk-form-large" type="text"
                                   name="title" v-model="translations[locale.language].title">
                        </div>
                    </div>
                    <div class="uk-form-row uk-form-stacked">
                        <label class="uk-form-label">{{ 'Content' | trans }}</label>

                        <div class="uk-form-controls">
                            <v-editor :value.sync="translations[locale.language].content"
                                      :options="{markdown : translations[locale.language].data.content_markdown}"></v-editor>
                        </div>
                    </div>

                    <div class="uk-form-row">
                        <label class="uk-form-label">{{ 'From name' | trans }} *</label>
                        <div class="uk-form-controls">
                            <input name="from_name" class="uk-width-1-1" v-model="translations[locale.language].data.from_name"/>
                        </div>
                    </div>

                    <div class="uk-form-row uk-form-horizontal">
                        <span class="uk-form-label">{{ 'Attachment' | trans }}</span>

                        <div class="uk-form-controls">
                            <input-file :file.sync="translations[locale.language].data.file" root="storage" :ext="['pdf','docx','doc','xls','xlsx']"></input-file>
                        </div>
                    </div>
                </li>
            </ul>

        </div>

    </div>

</template>

<script>
/*global _*/

import TranslationMixin from '../../../languagemanager/app/mixins/translation-mixin';
import FlagSource from '../../../languagemanager/app/mixins/flag-source';

export default {

    name: 'TextLanguage',

    mixins: [TranslationMixin, FlagSource,],

    props: {'text': Object,},

    data: () => _.merge({
        translations: {},
        languages: {},
        types: {},
        default_language: '',
        model: '',
        model_id: 0,
        type: 'emailsender.emailtext',
    }, window.$languageManager),

    created() {
        this.model = this.types[this.type].model;
        this.model_id = this.text.id;
        this.default_translation_data = {
            content_markdown: true,
            from_name: '',
            file: '',
        };
        this.setup();
        //set id for new items
        if (!this.text.id) {
            this.$watch('text.id', id => this.setNewId(id));
        }
    },

};

</script>
