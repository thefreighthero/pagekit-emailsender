<?php

namespace Bixie\Emailsender;

use Bixie\Emailsender\Emailtype\EmailtypeCollection;
use Bixie\Emailsender\Model\EmailLog;
use Bixie\Emailsender\Model\EmailText;
use Pagekit\Application as App;
use Pagekit\Mail\Message;
use Pagekit\Module\Module;
use Pagekit\Util\Arr;

class EmailsenderModule extends Module {

	/**
	 * {@inheritdoc}
	 */
	public function main (App $app) {

		$app['emailtypes'] = new EmailtypeCollection([
			'core.user.registration' => [
				'label' => 'Pagekit User registration',
				'classes' => ['user' => 'Pagekit\User\Model\User']
			]
		]);

	}

	/**
	 * @param string $type
	 * @param array $data
	 * @param array $roles
	 */
	public function sendTexts ($type, $data = [], $roles = []) {
		foreach ($this->loadTexts($type, $data, $roles) as $text) {
			$this->sendMail($text);
		}
	}

	/**
	 * @param string $type
	 * @param array $data
	 * @param array $roles
	 * @return EmailText[]
	 */
	public function loadTexts ($type, $data = [], $roles = []) {
		$query = EmailText::where(['type = ?'], [$type]);
		if (count($roles)) {
			$query->where(function ($query) use ($roles) {
				return $query->where('roles IS NULL')->whereInSet('roles', $roles, false, 'OR');
			});
		}
		/** @var EmailText $text */
		foreach ($texts = $query->get() as $text) {
			foreach ((array) $data as $key => $object) {
				$text->getEmailtype()->addObject($key, $object);
			}
		}
		return $texts;
	}

	/**
	 * @param EmailText $text
	 * @param array     $mail
	 * @return bool
	 * @throws EmailsenderException
	 */
	public function sendMail (EmailText $text, $mail = []) {
		$mail = [
			'from_name' => $text->get('from_name'),
			'from_email' => $text->get('from_email'),
			'recipients' => $text->getTo(Arr::get($mail, 'to', [])),
			'cc' => $text->getCc(Arr::get($mail, 'cc', [])),
			'bcc' => $text->getBcc(Arr::get($mail, 'bcc', [])),
			'subject' => @$mail['subject'] ? : $text->getSubject(),
			'content' => @$mail['content'] ? : $text->getContent()
		];

		if (empty($mail['recipients'])) {
			throw new EmailsenderException(__('No receivers for email!'));
		}

		$mailContent = App::content()->applyPlugins($mail['content'], ['markdown' => true]);
		$mailContent = App::view(sprintf('bixie/emailsender/mails/%s.php', $text->get('template', 'default')), ['mailContent' => $mailContent]);

		/** @var Message $message */
		$message = App::mailer()->create($mail['subject'], $mailContent, $mail['recipients'])
			->setFrom($mail['from_email'], $mail['from_name'])->setContentType('text/html');

		if (!empty($mail['cc'])) {
			$message->setCc($mail['cc']);
		}
		if (!empty($mail['bcc'])) {
			$message->setBcc($mail['bcc']);
		}

		$errors = [];
		$message->send($errors);

		if (count($errors)) {
			throw new EmailsenderException(__('Email failed for %addresses%', ['addresses' => implode(', ', $errors)]));
		}

		if ($this->config('save_logs', false)) {
			EmailLog::create($mail)->save(['type' => $text->type, 'sent' => new \DateTime()]);
		}

		return true;
	}

}
