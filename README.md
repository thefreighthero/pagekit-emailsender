# Bixie Email sender

Send emails from your extensions.

### Register EmailTexts
 
Add emailtypes from our extension on the `boot` event:

```php
$app->on('boot', function () use ($app) {
    //add mailtypes
    if (isset($app['emailtypes'])) {
        $app['emailtypes']->register([
            'name.site.mailtype' => [
                'label' => 'Mail sent on event 1',
                'classes' => [
                    'user' => 'Pagekit\User\Model\User'
                ]
            ],
            'name.site.mailtype2' => [
                'label' => 'Mail sent on event 2',
                'classes' => [
                    'invoice' => 'Your\Namespace\Model\Invoice',
                    'user' => 'Pagekit\User\Model\User'
                ],
                'values' => [
                    'file_type' => 'invoice',
                ]
            ]
        ]);
    }
}
```

Add classes to add shortcuts to those values in the mailtemplate. Extra variables can be passed in via the `values` key.

### Load templates

Load the templates with replaced variable placeholders to edit before sending.

```php
		$texts = App::module('bixie/emailsender')->loadTexts('name.site.mailtype1', [
			'invoice' => $invoice, 'user' => App::user()
		], [Role::ROLE_AUTHENTICATED]);

```

### Send Email

Send the email from wherever you're extensions logic needs it:

```php
        try {

            App::module('bixie/emailsender')->sendTexts('name.site.mailtype2', [
                'invoice' => $invoice, 'user' => App::user()
            ]);

        } catch (EmailsenderException $e) {
            //error handling
        }

```

Override the default template content with user-filled or customized values, or add extra addresses or files. The value `ext_key` is used for logging.

```php
        try {

            App::module('bixie/emailsender')->sendTexts('name.site.mailtype2', [
                'invoice' => $invoice, 'user' => App::user()
            ], [
                'subject' => $customsubject,
                'bcc' => $adminEmail,
                'files' => ['/var/www/myfile.pdf'],
                'ext_key' => 'mailtype2.' . $recordId
            ]);

        } catch (EmailsenderException $e) {
            //error handling
        }

```

### Retrieve log

The logs can be retrieved via the API, for instance via Vue resource:

```js
    this.$resource('api/emailsender/log').query({filter: {search: '', ext_key: 'mailtype2.34', order: 'sent desc'}, page: 0})
        .then(function (res) {
                var data = res.data;
                this.$set('logs', data.logs);
                this.$set('pages', data.pages);
                this.$set('count', data.count);
                this.$set('selected', []);
            }, function (res) {
                this.$notify(res.data.message || res.data, 'danger');
            }
        );
```

###HTML template

The base-template of this extension can be overridden in your theme. Create the file