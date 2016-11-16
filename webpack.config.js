module.exports = [

    {
        entry: {
            /*admin views*/
            "emailsender-text": "./app/views/admin/text.js",
            "emailsender-texts": "./app/views/admin/texts.js",
            "emailsender-logs": "./app/views/admin/logs.js",
            "emailsender-settings": "./app/views/admin/settings.js"
        },
        output: {
            filename: "./app/bundle/[name].js"
        },
        externals: {
            "lodash": "_",
            "jquery": "jQuery",
            "uikit": "UIkit",
            "vue": "Vue"
        },
        module: {
            loaders: [
                {test: /\.vue$/, loader: "vue"},
                {test: /\.html$/, loader: "vue-html"},
                {test: /\.js/, loader: 'babel', query: {presets: ['es2015']}}
            ]
        }

    }

];
