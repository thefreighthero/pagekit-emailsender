<?php

namespace Bixie\Emailsender\Controller;

use Bixie\Emailsender\EmailsenderModule;
use Bixie\Emailsender\Model\EmailText;
use Pagekit\Application as App;

/**
 * @Access("emailsender: send mails")
 */
class EmailsenderApiController {
    /**
     * @var EmailsenderModule
     */
    protected $module;

    /**
     * EmailsenderApiController constructor.
     */
    public function __construct () {
        $this->module = App::module('bixie/emailsender');
    }


    /**
     * @Route("/template", methods="POST")
     * @Request({"type": "string", "data": "array", "user_id": "int"}, csrf=true)
     * @param       $type
     * @param array $data
     * @param int   $user_id
     * @return array
     */
    public function templateAction($type, $data = [], $user_id = 0) {

        $texts = $this->module->loadTexts($type, $data, $user_id);

        /** @var EmailText $text */
        $text = reset($texts);

        return ['mail' => [
            'to' => $text->getTo(),
            'cc' => $text->getCc(),
            'bcc' => App::user()->hasAccess('emailsender: manage texts') ? $text->getBcc() : '',
            'subject' => $text->getSubject(),
            'content' => $text->getContent()
        ]];
    }

    /**
     * @Route("/sendmail", methods="POST")
     * @Request({"type": "string", "mail": "array", "data": "array", "user_id": "int"}, csrf=true)
     * @param string $type
     * @param array $mail
     * @param array $data
     * @param int   $user_id
     * @return array
     */
    public function sendmailAction($type, $mail, $data = [], $user_id = 0) {

        $texts = $this->module->loadTexts($type, $data, $user_id);

        /** @var EmailText $text */
        $text = reset($texts);

        try {

            $this->module->sendMail($text, $mail);

            return ['mail' => $mail, 'message' => __('Email successfully sent')];

        } catch (App\Exception $e) {
            return App::abort(400, $e->getMessage());
        }

    }

}
