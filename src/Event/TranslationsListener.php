<?php

namespace Bixie\Emailsender\Event;

use Bixie\Emailsender\Model\EmailText;
use Bixie\Languagemanager\Event\TranslateEvent;
use Bixie\Languagemanager\Model\Translation;
use Pagekit\Event\EventSubscriberInterface;


class TranslationsListener implements EventSubscriberInterface {
    /**
     * @param TranslateEvent $event
     * @param EmailText      $text
     */
    public function translateEmailText (TranslateEvent $event, $text) {

        if ($translation = Translation::findModelTranslation('Bixie\Emailsender\Model\EmailText', $text->id, $event->getLanguage())) {
            if ($translation->title) {
                $text->subject = $translation->title;
            }
            if ($translation->content) {
                $text->content = $translation->content;
            }
            if ($from_name = $translation->get('from_name')) {
                $text->set('from_name', $from_name);
            }
            if ($file = $translation->get('file')) {
                $text->set('file', $file);
            }
        }

    }

    /**
     * {@inheritdoc}
     */
    public function subscribe () {
        return [
            'translate.emailsender_emailtext' => 'translateEmailText',
        ];
    }

}