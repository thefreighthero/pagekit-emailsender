<?php

return [

	'name' => 'bixie/emailsender',

	'type' => 'extension',

	'main' => 'Bixie\\Emailsender\\EmailsenderModule',

	'autoload' => [

		'Bixie\\Emailsender\\' => 'src'

	],
	'routes' => [

		'/emailsender' => [
			'name' => '@emailsender',
			'controller' => [
				'Bixie\\Emailsender\\Controller\\EmailsenderController',
				'Bixie\\Emailsender\\Controller\\TextController'
			]
		],
		'/api/emailsender' => [
			'name' => '@emailsender/api',
			'controller' => [
				'Bixie\\Emailsender\\Controller\\TextApiController',
				'Bixie\\Emailsender\\Controller\\LogApiController'
			]
		]

	],

	'resources' => [

		'bixie/emailsender:' => ''

	],

	'config' => [
		'embed_images' => true,
		'embed_images_maxsize' => 1024,
		'save_logs' => true,
		'from_name' => '',
		'from_email' => ''
	],

	'menu' => [

		'emailsender' => [
			'label' => 'Email Sender',
			'icon' => 'packages/bixie/emailsender/icon.svg',
			'url' => '@emailsender',
			'access' => 'emailsender: manage texts',
			'active' => '@emailsender(/*)'
		],

		'emailsender: texts' => [
			'label' => 'Texts',
			'parent' => 'emailsender',
			'url' => '@emailsender',
			'access' => 'emailsender: manage forms',
			'active' => '@emailsender(/text/edit)?'
		],

		'emailsender: logs' => [
			'label' => 'Logs',
			'parent' => 'emailsender',
			'url' => '@emailsender/logs',
			'access' => 'emailsender: manage settings',
			'active' => '@emailsender/logs(/edit)?'
		],

		'emailsender: settings' => [
			'label' => 'Settings',
			'parent' => 'emailsender',
			'url' => '@emailsender/settings',
			'access' => 'emailsender: manage settings',
			'active' => '@emailsender/settings'
		]

	],

	'permissions' => [

		'emailsender: manage settings' => [
			'title' => 'Manage settings'
		],

		'emailsender: manage texts' => [
			'title' => 'Manage texts'
		],

		'emailsender: manage logs' => [
			'title' => 'Manage logs'
		]

	],

	'settings' => '@emailsender/settings',

	'events' => [
	]

];
