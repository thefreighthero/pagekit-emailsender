<?php

namespace Bixie\Emailsender;

use Bixie\Emailsender\Emailtype\EmailtypeCollection;
use Bixie\Emailsender\Event\EmailPrepareEvent;
use Bixie\Emailsender\Model\EmailLog;
use Bixie\Emailsender\Model\EmailText;
use Bixie\Emailsender\Plugin\MailImagesPlugin;
use Bixie\Emailsender\Plugin\MailLinksPlugin;
use Pagekit\Application as App;
use Pagekit\Mail\Message;
use Pagekit\Module\Module;
use Pagekit\Util\Arr;

class EmailsenderModule extends Module {

	/**
	 * {@inheritdoc}
	 */
	public function main (App $app) {
		$app->subscribe(
			new MailImagesPlugin($this->config),
			new MailLinksPlugin($this->config)
		);

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
	public function sendTexts ($type, $data = [], $mail = [], $roles = []) {
		foreach ($this->loadTexts($type, $data, $roles) as $text) {
			$this->sendMail($text, $mail);
		}
	}

	/**
	 * @param string $type
	 * @param array $data
	 * @param array $roles
	 * @return EmailText[]
	 */
	public function loadTexts ($type, $data = [], $roles = []) {
		$query = EmailText::where(['type LIKE ?'], ["$type%"]);
		if (count($roles)) {
			$query->where(function ($query) use ($roles) {
				return $query->where('roles IS NULL')->whereInSet('roles', $roles, false, 'OR');
			});
		}
		/** @var EmailText $text */
		foreach ($texts = $query->get() as $text) {
			$emailType = $text->getEmailtype();
			foreach ((array) $data as $key => $object) {
				$emailType->addData($key, $object);
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
			'subject' => Arr::get($mail, 'subject') ? : $text->getSubject(),
			'content' => Arr::get($mail, 'content') ? : $text->getContent(),
			'string_attachments' => Arr::get($mail, 'string_attachments') ? : [],
			'files' => Arr::get($mail, 'files') ? : [],
			'data' => [],
			'ext_key' => Arr::get($mail, 'ext_key') ? : ''
		];

		if (empty($mail['recipients'])) {
			throw new EmailsenderException(__('No receivers for email!'));
		}

		$mail['content'] = App::content()->applyPlugins(nl2br($mail['content']), ['markdown' => true]);
		/** @var Message $message */
		$message = App::mailer()->create($mail['subject'], $mail['content'], $mail['recipients'])
			->setFrom($mail['from_email'], $mail['from_name']);

		//apply template and check images and links
		$mailContent = App::view(sprintf('bixie/emailsender/mails/%s.php', $text->get('template', 'default')), [
			'mailContent' => $mail['content']
		]);
		$mailContent = App::trigger(new EmailPrepareEvent('emailsender.prepare', $mailContent, $message))->getContent();
		$message->setBody($mailContent, 'text/html');

		if (!empty($mail['cc'])) {
			$message->setCc($mail['cc']);
		}
		if (!empty($mail['bcc'])) {
			$message->setBcc($mail['bcc']);
		}
		
		if (!empty($mail['string_attachments'])) {
			foreach ($mail['string_attachments'] as $string_attachment) {
				$message->attachData($string_attachment['data'], $string_attachment['name'], Arr::get($string_attachment, 'mime'));
				$mail['data']['attachments'][] = $string_attachment['name'];
			}
		}
		if (!empty($mail['files'])) {
			foreach ($mail['files'] as $file_path) {
				if ($path =  $this->normalizePath(App::path() . '/' . $file_path) and file_exists($path)) {
					$message->attachFile($path, basename($path));
					$mail['data']['attachments'][] = basename($path);
				}
			}
		}
		if (!empty($mailImages)) {
			foreach ($mailImages as $image) {
				$message->AddEmbeddedImage($image['path'], $image['name'], $image['filename'], $image['encoding'], $image['mimetype']);

				if ($path =  $this->normalizePath(App::path() . '/' . $file_path) and file_exists($path)) {
					$message->attachFile($path, basename($path));
					$mail['data']['attachments'][] = basename($path);
				}
			}
		}

		$errors = [];
		$message->send($errors);

		if (count($errors)) {
			throw new EmailsenderException(__('Email failed for %addresses%', ['addresses' => implode(', ', $errors)]));
		}

		if ($this->config('save_logs', false)) {
			EmailLog::create($mail)->save([
				'type' => $text->type,
				'sent' => new \DateTime()
			]);
		}

		return true;
	}

	/**
	 * Normalizes the given path
	 * @param  string $path
	 * @return string
	 */
	protected function normalizePath ($path) {
		$path = str_replace(['\\', '//'], '/', $path);
		$prefix = preg_match('|^(?P<prefix>([a-zA-Z]+:)?//?)|', $path, $matches) ? $matches['prefix'] : '';
		$path = substr($path, strlen($prefix));
		$parts = array_filter(explode('/', $path), 'strlen');
		$tokens = [];

		foreach ($parts as $part) {
			if ('..' === $part) {
				array_pop($tokens);
			} elseif ('.' !== $part) {
				array_push($tokens, $part);
			}
		}

		return $prefix . implode('/', $tokens);
	}

}
