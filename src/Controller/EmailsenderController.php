<?php

namespace Bixie\Emailsender\Controller;

use Pagekit\Application as App;
use Bixie\Formmaker\Model\Form;
use Bixie\Formmaker\Model\Submission;
use Pagekit\User\Model\Role;

/**
 * @Access(admin=true)
 */
class EmailsenderController {

	/**
	 * @Route("/", methods="GET")
	 * @Request({"filter": "array", "page":"int"})
	 */
	public function indexAction ($filter = [], $page = null) {

		return [
			'$view' => [
				'title' => __('Email Sender'),
				'name' => 'bixie/emailsender/admin/texts.php'
			],
			'$data' => [
				'types' => App::get('emailtypes')->all(),
				'roles' => array_values(Role::findAll()),
				'config' => [
					'filter' => (object) $filter,
					'page' => $page
				]
			]
		];
	}

	/**
	 * @Route("/logs", methods="GET")
	 * @Request({"filter": "array", "page":"int"})
	 */
	public function logsAction ($filter = null, $page = null) {

		return [
			'$view' => [
				'title' => __('Email logs'),
				'name' => 'bixie/emailsender/admin/logs.php'
			],
			'$data' => [
				'types' => App::get('emailtypes')->all(),
				'config' => [
					'filter' => $filter,
					'page' => $page
				]
			]
		];
	}

	/**
	 * @Access("system: manage settings")
	 */
	public function settingsAction () {

		return [
			'$view' => [
				'title' => __('Email sendersettings'),
				'name' => 'bixie/emailsender/admin/settings.php'
			],
			'$data' => [
				'types' => App::get('emailtypes')->all(),
				'config' => App::module('bixie/emailsender')->config()
			]
		];
	}


}
