<?php

namespace Bixie\Emailsender\Controller;

use Pagekit\Application as App;
use Pagekit\Kernel\Exception\NotFoundException;
use Bixie\Emailsender\Model\Emailtext;
use Pagekit\User\Model\Role;

/**
 * @Access("emailsender: manage texts", admin=true)
 * @Route("text", name="text")
 */
class TextController {

	/**
	 * @Route("/edit", name="edit")
	 * @Request({"id": "int"})
	 */
	public function editAction ($id = 0) {

		App::get('emailtypes')->get('core.user.registration')->getKeys();

		if (!$text = Emailtext::find($id)) {

			if ($id == 0) {
				$text = Emailtext::create();
			}

		}

		if (!$text) {
			App::abort(404, __('Text not found.'));
		}

		return [
			'$view' => [
				'title' => __('Text'),
				'name' => 'bixie/emailsender/admin/text.php'
			],
			'$data' => [
				'config' => App::module('bixie/emailsender')->config(),
				'roles' => array_values(Role::findAll()),
				'types' => App::get('emailtypes')->all(),
				'text' => $text
			]
		];
	}

}
