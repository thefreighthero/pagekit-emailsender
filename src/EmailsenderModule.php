<?php

namespace Bixie\Emailsender;

use Bixie\Emailsender\Emailtype\EmailtypeCollection;
use Bixie\Emailsender\Event\EmailPrepareEvent;
use Bixie\Emailsender\Event\TranslationsListener;
use Bixie\Emailsender\Model\EmailLog;
use Bixie\Emailsender\Model\EmailText;
use Bixie\Emailsender\Plugin\ImpersonatePlugin;
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
            new TranslationsListener,
            new MailImagesPlugin($this->config),
			new MailLinksPlugin($this->config)
		);

		$app['emailtypes'] = new EmailtypeCollection([
			'core.user.registration' => [
				'label' => 'Pagekit User registration',
				'classes' => ['user' => 'Pagekit\User\Model\User']
			]
		]);
        $app->on('boot', function () use ($app) {
            //register type with languagemanager
            if (isset($app['translationtypes'])) {
                $app['translationtypes']->register([
                    'emailsender.emailtext' => [
                        'label' => 'Emailsender Emailtext',
                        'model' => 'Bixie\Emailsender\Model\EmailText',
                        'event_model' => 'emailsender_emailtext',
                        'edit_link' => '@emailsender/text/edit',
                    ],
                ]);
            }
        });

    }

    /**
     * @param int     $id
     * @param array  $data
     * @param array  $mail
     * @param int    $user_id
     * @param array  $roles
     */
	public function sendText ($id, $data = [], $mail = [], $user_id = 0, $roles = []) {
		if ($text = $this->loadText($id, $data, $user_id, $roles)) {
			$this->sendMail($text, $mail);
		}
	}

    /**
     * @param string $type
     * @param array  $data
     * @param array  $mail
     * @param int    $user_id
     * @param array  $roles
     */
	public function sendTexts ($type, $data = [], $mail = [], $user_id = 0, $roles = []) {
		foreach ($this->loadTexts($type, $data, $user_id, $roles) as $text) {
			$this->sendMail($text, $mail);
		}
	}

    /**
     * @param string $id
     * @param array  $data
     * @param int    $user_id
     * @param array  $roles
     * @return EmailText
     */
	public function loadText ($id, $data = [], $user_id = 0, $roles = []) {

        $user = $user_id ? App::auth()->getUserProvider()->find($user_id) : App::user();
        if ($userprofile = App::module('bixie/userprofile')) {
            $user = \Bixie\Userprofile\User\ProfileUser::load();
        }

        $query = EmailText::where(compact('id'));
		if (count($roles)) {
			$query->where(function ($query) use ($roles) {
				return $query->where('roles IS NULL')->whereInSet('roles', $roles, false, 'OR');
			});
		}
		/** @var EmailText $text */
		if ($text = $query->first()) {
			$emailType = $text->getEmailtype();
			foreach (array_merge(['user' => $user], (array) $data) as $key => $object) {
				$emailType->addData($key, $object);
			}
		}
		return $text;
	}

    /**
     * @param string $type
     * @param array  $data
     * @param int    $user_id
     * @param array  $roles
     * @return EmailText[]
     */
	public function loadTexts ($type, $data = [], $user_id = 0, $roles = []) {

        $user = $user_id ? App::auth()->getUserProvider()->find($user_id) : App::user();
        if ($userprofile = App::module('bixie/userprofile')) {
            $user = \Bixie\Userprofile\User\ProfileUser::load();
        }

        $query = EmailText::where(['type LIKE ?'], ["$type%"])->orderBy('type', 'ASC')->orderBy('subject', 'ASC');
		if (count($roles)) {
			$query->where(function ($query) use ($roles) {
				return $query->where('roles IS NULL')->whereInSet('roles', $roles, false, 'OR');
			});
		}
		/** @var EmailText $text */
		foreach ($texts = $query->get() as $text) {
			$emailType = $text->getEmailtype();
			foreach (array_merge(['user' => $user], (array) $data) as $key => $object) {
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
        $errors = [];
	    $files = Arr::get($mail, 'files', []);
	    if ($file = $text->get('file')) {
            $files[] = $file;
        }

        $to = [];
        if(isset($mail['to'])) {
            if($mail['to'] != '') {
                $to[] = $mail['to'];
            }
        } else {
            $to = $text->getTo();
        }
        $text->set('to', $to);

        $cc = [];
        if(isset($mail['cc'])) {
            if($mail['cc'] != '') {
                $cc[] = $mail['cc'];
            }
        } else {
            $cc = $text->getCc();
        }
        $text->set('cc', $cc);

        $bcc = [];
        if(isset($mail['bcc'])) {
            if($mail['bcc'] != '') {
                $bcc[] = $mail['bcc'];
            }
        } else {
            $bcc = $text->getBcc();
        }
        $text->set('bcc', $bcc);

        $mail = [
            'from_name' => Arr::get($mail, 'from_name') ? : $text->getFromName(),
            'from_email' => Arr::get($mail, 'from_email') ? : $text->getFromEmail(),
            'recipients' => $text->getTo(),
            'cc' => $text->getCc(),
            'bcc' => $text->getBcc(),
//            'recipients' => $text->getTo(Arr::get($mail, 'to', [])),
//            'cc' => $text->getCc(Arr::get($mail, 'cc', [])),
//            'bcc' => $text->getBcc(Arr::get($mail, 'bcc', [])),
            'subject' => Arr::get($mail, 'subject') ? : $text->getSubject(),
            'content' => Arr::get($mail, 'content') ? : $text->getContent(),
            'string_attachments' => Arr::get($mail, 'string_attachments') ? : [],
            'files' => $files,
            'data' => array_merge(['attachments' => []], Arr::get($mail, 'data', [])),
            'ext_key' => Arr::get($mail, 'ext_key') ? : ''
        ];

		if (empty($mail['recipients'])) {
			throw new EmailsenderException(__('No receivers for email!'));
		}

		//setting from on the message is overridden by system ImpersonatePlugin
        App::mailer()->registerPlugin(new ImpersonatePlugin($mail['from_email'], $mail['from_name']));

		$mail['content'] = App::content()->applyPlugins($mail['content'], [
		    'markdown' => ['gfm' => true,],
        ]);

        /** @var Message $message */
        $message = App::mailer()->create($mail['subject'], $mail['content'], $mail['recipients']);

        //apply template and check images and links
        $mailContent = App::view(sprintf('bixie/emailsender/mails/%s.php', $text->get('template', 'default')), [
            'mailContent' => $mail['content'],
            'plainText' => substr(strip_tags($mail['content']), 0, 100),
        ]);
        $mailContent = App::trigger(new EmailPrepareEvent('emailsender.prepare', $mailContent, $message, $text))->getContent();

        // Use brevo for mails or just juse good old Swiftmailer
        if ($this->config('use_brevo', false)) {

            $sendinblue = App::module('sendinblue');
            $errors = $sendinblue->sendMail($mail, $mailContent);

        // Use Swiftmailer.
        } else {
            //Swift can throw exceptions on validating the addresses
            try {

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
                        if ($path = $this->normalizePath($file_path) and file_exists($path)) {
                            $message->attachFile($path, basename($path));
                            $mail['data']['attachments'][] = basename($path);
                        }
                    }
                }
                if (!empty($mailImages)) {
                    foreach ($mailImages as $image) {
                        $message->AddEmbeddedImage($image['path'], $image['name'], $image['filename'], $image['encoding'], $image['mimetype']);
                    }
                }

                $message->send($errors);

            } catch (\Swift_SwiftException $e) {
                //todo detect dev env properly
                if (empty($_SERVER['WINDIR'])) {
                    throw new EmailsenderException($e->getMessage(), $e->getCode(), $e);
                }
            }

        }




        //todo detect dev env properly
		if (count($errors) && empty($_SERVER['WINDIR'])) {
			throw new EmailsenderException(__('Email failed for %addresses%', ['%addresses%' => implode(', ', $errors)]));
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
        if ($prefix) {
            return App::locator()->get($path);
        }
        $path = App::path() . '/' . $path;
        $parts = array_filter(explode('/', str_replace('\\', '/', $path)), 'strlen');
		$tokens = [];

		foreach ($parts as $part) {
			if ('..' === $part) {
				array_pop($tokens);
			} elseif ('.' !== $part) {
				array_push($tokens, $part);
			}
		}

		return (!isset($tokens[0]) || stripos($tokens[0], ':') === false ? '/' : '') . implode('/', $tokens);
	}

}
