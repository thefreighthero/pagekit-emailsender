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
            ],
            'name.site.mailtype2' => [
                'label' => 'Mail sent with added data',
                'classes' => [
                    'order' => 'Your\Namespace\Model\Order',
                    'invoice' => 'Your\Namespace\Model\Invoice',
                ],
                'values' => [
                    'file_name' => 'invoice',
                    'note' => 'note',
                ],
            ],
            'name.site.mailtype3' => [
                'label' => 'Mail sent with custom user',
                'classes' => [
                    'user' => 'Your\Namespace\Model\User',
                ],
            ],
        ]);
    }
});
```
The user variables are loaded from the Pagekit core User or if available the [Bixie Userprofile](https://github.com/Bixie/pagekit-userprofile) 
ProfileUser. Add a class ro the key `user` to define a custom user class.
Add classes to add shortcuts to those values in the mailtemplate. Extra variables can be passed in via the `values` key.

### Load templates

Load the templates with replaced variable placeholders to edit before sending. Optionally specify a user ID to load the data from. The current
Pagekit user will be used if not specified.

```php
		$texts = App::module('bixie/emailsender')->loadTexts('name.site.mailtype1', [], $user_id);

```
Add custom data to render in the mailtemplate:

```php
		$texts = App::module('bixie/emailsender')->loadTexts('name.site.mailtype2', [
			'order' => $order,
			'invoice' => $invoice,
			'values' => [
                'file_name' => 'myfile.pdf',
                'note' => 'My personal note',
	    	]
		], $user_id);

```

### Send Email

Send the email from wherever you're extensions logic needs it:

```php
        try {

            App::module('bixie/emailsender')->sendTexts('name.site.mailtype1', [], $user_id);

        } catch (EmailsenderException $e) {
            //error handling
        }

```

Override the default template content with user-filled or customized values, or add extra addresses or files. The 
value `ext_key` is used for logging.

```php
        try {

            App::module('bixie/emailsender')->sendTexts('name.site.mailtype2', [
                'order' => $order,
                'invoice' => $invoice,
                'values' => [
                    'file_name' => 'myfile.pdf',
                    'note' => 'My personal note',
                ]
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

### Manipulate messages before sending

You can first retrieve the prefilled templates from Emailsender, send those to your UI for editing, and then send the final text.

```php
    $templates = App::module('bixie/emailsender')->loadTexts('name.site.mailtype2');
    $text = reset($templates);
    
    $mail = [
        'to' => $text->getTo(),
        'cc' => $text->getCc('extra@ccaddress.com'),
        'bcc' => App::user()->hasAccess('emailsender: manage texts') ? $text->getBcc() : '',
        'subject' => $text->getSubject(),
        'content' => $text->getContent()
    ]];

```



### Retrieve log

The logs can be retrieved via the API, for instance via Vue resource:

```js
    this.$resource('api/emailsender/log').query({filter: {search: '', ext_key: 'mailtype2.34', order: 'sent desc'}, page: 0})
        .then(res => {
                var data = res.data;
                this.$set('logs', data.logs);
                this.$set('pages', data.pages);
                this.$set('count', data.count);
                this.$set('selected', []);
            }, res => this.$notify(res.data.message || res.data, 'danger'));
```

###HTML template

The base-template of this extension can be overridden in your theme. Create the file `views/bixie/emailsender/mails/default.php` to 
replace the default template.

###Email interface

The [Bixie Pagekit Framework](https://github.com/Bixie/pk-framework) provides a Vue component that lets you integrate the emailsender 
in any view or template.
Retrieve a list of templates in your controller via the module. You can pass in a filter for the types to fetch.

```php
 $templates = array_values(App::module('bixie/emailsender')->loadTexts('name.site.'));
```

Then render the component in your view:

```html
        <email-communication :templates="templates" :ext_key="`name.site.item.${item.id}`"></email-communication>

```

Property | Type | value 
---------|------|-------
templates | Array                   | Array of emailsender Templates
ext_key   |  String                 | External key to reference the emials with
resource  |  String _optional_      | Custom api resource for rendering custom templates
id        |  String,Number _optional_| Id to call the custom resource with
user_id   |  Number _optional_      | User id to use for user data. If not provided, the current user will be used.
email-data | Object _optional_      | Additional data to pass to the template render function
attachments | Array _optional_      | string of filenames to show in the interface

A fully customized component could look like this:

```html
        <email-communication :templates="templates"
                             :ext_key="`name.site.${item.id}`"
                             resource="api/mymodule/email"
                             :id="item.id"
                             :user_id="item.user_id"
                             :email-data="emailData"
                             :attachments="attachments"></email-communication>

```

The component shows the log of sent messages and an interface to compose new messages based on the prefilled email templates.

###Message parsing

Emailsender will parse the messages and replace all `$$value.key$$` placeholders with the values passed to the mail function. 
Emailsender uses the `json_encode` function to retrieve the values from the objects.

Links in the email that are relative (eg `/contact-us`) are automatically prefixed with the domain name and 
host (`http://www.domain.com/contact-us`). Optionally the urls can be suffixed with a parameter to track the source of 
the mails (eg `http://www.domain.com/contact-us?utm_source=automail`).

Images in the email can be replaced with inline data to prevent the annoying warnings in email clients. A maximum size can 
be set to prevent emails from getting too large.
